package ph.queenscup.customer.ui.chat

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import ph.queenscup.customer.data.api.ApiClient
import ph.queenscup.customer.data.model.ChatMessagePayload
import ph.queenscup.customer.data.model.ChatRequest

data class ChatState(
    val messages: List<ChatMessagePayload> = emptyList(),
    val draft: String = "",
    val busy: Boolean = false,
    val loaded: Boolean = false,
)

/**
 * The assistant, answered by the server so it can speak about the live menu
 * and this customer's own reservations. A signed-in customer's conversation
 * is the same one they see on the website.
 */
class ChatViewModel(app: Application) : AndroidViewModel(app) {

    private val api = ApiClient.service

    private val _state = MutableStateFlow(ChatState())
    val state: StateFlow<ChatState> = _state.asStateFlow()

    fun load() {
        if (_state.value.loaded) return
        _state.value = _state.value.copy(loaded = true)

        viewModelScope.launch {
            val history = runCatching { api.chatHistory() }.getOrNull()
            val messages = history?.data.orEmpty()

            _state.value = _state.value.copy(
                messages = messages.ifEmpty {
                    listOf(
                        ChatMessagePayload(
                            author = "bot",
                            body = "Hello! 👑 I'm the Queen's Cup assistant. What can I help you with?",
                            quickReplies = listOf(
                                "See the menu",
                                "How do I reserve?",
                                "My reservations",
                                "Opening hours",
                            ),
                        )
                    )
                }
            )
        }
    }

    fun onDraft(value: String) {
        _state.value = _state.value.copy(draft = value)
    }

    fun send(text: String = _state.value.draft) {
        val message = text.trim()
        if (message.isEmpty() || _state.value.busy) return

        _state.value = _state.value.copy(
            messages = _state.value.messages + ChatMessagePayload("customer", message),
            draft = "",
            busy = true,
        )

        viewModelScope.launch {
            val reply = runCatching { api.chatSend(ChatRequest(message)) }.getOrNull()

            val botMessage = reply?.let {
                ChatMessagePayload("bot", it.body, it.quickReplies)
            } ?: ChatMessagePayload(
                "bot",
                "I could not reach the shop just now. Please try again shortly.",
            )

            _state.value = _state.value.copy(
                messages = _state.value.messages + botMessage,
                busy = false,
            )
        }
    }
}

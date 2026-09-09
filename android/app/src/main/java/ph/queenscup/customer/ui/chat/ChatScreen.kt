package ph.queenscup.customer.ui.chat

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.itemsIndexed
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.material3.AssistChip
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.Icon
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Send
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.AnnotatedString
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import ph.queenscup.customer.data.model.ChatMessagePayload

/**
 * The assistant, matching the floating helper on the website.
 *
 * Replies arrive as light HTML (line breaks and bold), which is flattened for
 * display rather than rendered, so nothing from the wire can affect layout.
 */
@Composable
fun ChatScreen(viewModel: ChatViewModel) {
    val state by viewModel.state.collectAsStateWithLifecycle()
    val listState = rememberLazyListState()

    LaunchedEffect(Unit) { viewModel.load() }

    LaunchedEffect(state.messages.size) {
        if (state.messages.isNotEmpty()) listState.animateScrollToItem(state.messages.lastIndex)
    }

    Column(Modifier.fillMaxSize()) {
        Surface(tonalElevation = 2.dp) {
            Column(Modifier.fillMaxWidth().padding(16.dp)) {
                Text("Assistant", style = MaterialTheme.typography.titleMedium)
                Text(
                    "Menu, reservations and directions",
                    style = MaterialTheme.typography.bodySmall,
                )
            }
        }

        if (state.busy) LinearProgressIndicator(Modifier.fillMaxWidth())

        LazyColumn(
            state = listState,
            modifier = Modifier.weight(1f).fillMaxWidth().padding(horizontal = 12.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp),
            contentPadding = androidx.compose.foundation.layout.PaddingValues(vertical = 12.dp),
        ) {
            itemsIndexed(state.messages) { _, message ->
                Bubble(message) { viewModel.send(it) }
            }
        }

        Surface(tonalElevation = 3.dp) {
            Row(
                Modifier.fillMaxWidth().padding(10.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp),
                verticalAlignment = Alignment.CenterVertically,
            ) {
                OutlinedTextField(
                    value = state.draft,
                    onValueChange = viewModel::onDraft,
                    placeholder = { Text("Ask about the menu…") },
                    singleLine = true,
                    modifier = Modifier.weight(1f),
                )
                IconButton(onClick = { viewModel.send() }, enabled = !state.busy) {
                    Icon(Icons.AutoMirrored.Filled.Send, contentDescription = "Send")
                }
            }
        }
    }
}

@Composable
private fun Bubble(message: ChatMessagePayload, onQuickReply: (String) -> Unit) {
    val fromCustomer = message.author == "customer"

    Column(
        Modifier.fillMaxWidth(),
        horizontalAlignment = if (fromCustomer) Alignment.End else Alignment.Start,
    ) {
        Card(
            colors = CardDefaults.cardColors(
                containerColor = if (fromCustomer) {
                    MaterialTheme.colorScheme.primary
                } else {
                    MaterialTheme.colorScheme.surfaceVariant
                }
            )
        ) {
            Text(
                AnnotatedString(flatten(message.body)),
                modifier = Modifier.padding(11.dp),
                style = MaterialTheme.typography.bodyMedium,
                color = if (fromCustomer) {
                    MaterialTheme.colorScheme.onPrimary
                } else {
                    MaterialTheme.colorScheme.onSurfaceVariant
                },
            )
        }

        if (message.quickReplies.isNotEmpty()) {
            Row(
                Modifier.fillMaxWidth().padding(top = 6.dp),
                horizontalArrangement = Arrangement.spacedBy(6.dp),
            ) {
                // Two per row keeps the labels readable on a narrow phone.
                message.quickReplies.take(2).forEach { reply ->
                    Box(Modifier.weight(1f)) {
                        AssistChip(onClick = { onQuickReply(reply) }, label = { Text(reply) })
                    }
                }
            }

            if (message.quickReplies.size > 2) {
                Row(
                    Modifier.fillMaxWidth().padding(top = 6.dp),
                    horizontalArrangement = Arrangement.spacedBy(6.dp),
                ) {
                    message.quickReplies.drop(2).take(2).forEach { reply ->
                        Box(Modifier.weight(1f)) {
                            AssistChip(onClick = { onQuickReply(reply) }, label = { Text(reply) })
                        }
                    }
                }
            }
        }
    }
}

/** Turns the server's light HTML into plain text for a Compose bubble. */
private fun flatten(html: String): String =
    html.replace("<br><br>", "\n\n")
        .replace("<br>", "\n")
        .replace(Regex("</?strong>"), "")
        .replace("&amp;", "&")
        .replace("&lt;", "<")
        .replace("&gt;", ">")
        .replace("&#39;", "'")
        .replace("&quot;", "\"")
        .trim()

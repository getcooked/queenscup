package ph.queenscup.customer.ui.account

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch
import ph.queenscup.customer.data.api.ApiClient
import ph.queenscup.customer.data.local.SessionStore
import ph.queenscup.customer.data.model.AuthRequest
import ph.queenscup.customer.data.model.AuthUser
import ph.queenscup.customer.data.model.EmailRequest
import ph.queenscup.customer.data.model.VerifyRequest
import retrofit2.HttpException

/** Which part of signing in the customer is looking at. */
enum class AuthStep { SIGN_IN, REGISTER, VERIFY }

data class AuthState(
    val step: AuthStep = AuthStep.SIGN_IN,
    val name: String = "",
    val email: String = "",
    val contact: String = "",
    val password: String = "",
    val code: String = "",
    val busy: Boolean = false,
    val error: String? = null,
    val notice: String? = null,
    val signedIn: AuthUser? = null,
)

/**
 * Sign up and sign in, following the same rules as the website: an emailed
 * six digit code confirms the address before the account can be used.
 */
class AuthViewModel(app: Application) : AndroidViewModel(app) {

    private val session = SessionStore(app)
    private val api = ApiClient.service

    private val _state = MutableStateFlow(AuthState())
    val state: StateFlow<AuthState> = _state.asStateFlow()

    init {
        // A stored token means the customer is already signed in; confirm it
        // is still good rather than trusting it blindly.
        viewModelScope.launch {
            val token = session.token.first()
            if (!token.isNullOrBlank()) {
                // saveToken suspends, so this is handled with plain control
                // flow rather than runCatching's non-suspending callbacks.
                val user = runCatching { api.me() }.getOrNull()

                if (user != null) {
                    _state.value = _state.value.copy(signedIn = user)
                } else {
                    session.saveToken(null)
                }
            }
        }
    }

    fun onName(value: String) = update { it.copy(name = value, error = null) }
    fun onEmail(value: String) = update { it.copy(email = value.trim(), error = null) }
    fun onContact(value: String) = update { it.copy(contact = value, error = null) }
    fun onPassword(value: String) = update { it.copy(password = value, error = null) }
    fun onCode(value: String) = update { it.copy(code = value.filter(Char::isDigit).take(6), error = null) }

    fun showStep(step: AuthStep) = update { it.copy(step = step, error = null, notice = null) }

    fun register() {
        val s = _state.value
        if (s.name.isBlank() || s.email.isBlank() || s.password.length < 8) {
            update { it.copy(error = "Enter your name, email and a password of at least 8 characters.") }
            return
        }

        run(
            block = {
                val started = api.register(
                    AuthRequest(
                        name = s.name.trim(),
                        email = s.email,
                        password = s.password,
                        contactNumber = s.contact.ifBlank { null },
                    )
                )
                update {
                    it.copy(step = AuthStep.VERIFY, notice = started.message ?: "We sent a code to ${started.email}.")
                }
            }
        )
    }

    fun signIn() {
        val s = _state.value
        if (s.email.isBlank() || s.password.isBlank()) {
            update { it.copy(error = "Enter your email and password.") }
            return
        }

        run(
            block = {
                val response = api.login(AuthRequest(email = s.email, password = s.password))
                finish(response.token, response.user)
            },
            onHttpError = { code ->
                // 409 means the address was never confirmed. A fresh code has
                // already been sent, so move them straight to that step.
                if (code == 409) {
                    update {
                        it.copy(
                            step = AuthStep.VERIFY,
                            notice = "Confirm your email first. We sent a new code.",
                            error = null,
                        )
                    }
                    true
                } else {
                    false
                }
            }
        )
    }

    fun verify() {
        val s = _state.value
        if (s.code.length != 6) {
            update { it.copy(error = "Enter the 6 digit code from your email.") }
            return
        }

        run(
            block = {
                val response = api.verify(VerifyRequest(email = s.email, code = s.code))
                finish(response.token, response.user)
            }
        )
    }

    fun resend() {
        run(block = {
            api.resendCode(EmailRequest(_state.value.email))
            update { it.copy(notice = "A new code is on its way.") }
        })
    }

    fun signOut() {
        viewModelScope.launch {
            runCatching { api.logout() }
            session.saveToken(null)
            _state.value = AuthState()
        }
    }

    private suspend fun finish(token: String, user: AuthUser) {
        session.saveToken(token)
        session.saveCustomer(user.fullName, user.contactNumber)
        _state.value = _state.value.copy(signedIn = user, busy = false, code = "", password = "", error = null)
    }

    /**
     * Runs a call, showing progress and turning a validation failure into the
     * message the server actually gave rather than a generic one.
     */
    private fun run(
        block: suspend () -> Unit,
        onHttpError: ((Int) -> Boolean)? = null,
    ) {
        viewModelScope.launch {
            _state.value = _state.value.copy(busy = true, error = null, notice = null)
            try {
                block()
                _state.value = _state.value.copy(busy = false)
            } catch (e: HttpException) {
                val body = runCatching { e.response()?.errorBody()?.string() }.getOrNull()
                val handled = onHttpError?.invoke(e.code()) ?: false
                _state.value = _state.value.copy(
                    busy = false,
                    error = if (handled) null else firstMessage(body) ?: "That did not work. Please try again.",
                )
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    busy = false,
                    error = "Could not reach the shop. Check your connection.",
                )
            }
        }
    }

    /** Pulls the first human message out of a Laravel validation response. */
    private fun firstMessage(body: String?): String? {
        if (body.isNullOrBlank()) return null

        val errors = Regex("\"errors\"\\s*:\\s*\\{.*?\\[\\s*\"(.*?)\"").find(body)?.groupValues?.getOrNull(1)
        if (!errors.isNullOrBlank()) return errors.replace("\\/", "/")

        return Regex("\"message\"\\s*:\\s*\"(.*?)\"").find(body)?.groupValues?.getOrNull(1)
    }

    private fun update(transform: (AuthState) -> AuthState) {
        _state.value = transform(_state.value)
    }
}

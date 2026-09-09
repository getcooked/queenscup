package ph.queenscup.customer

import android.app.Application
import android.app.NotificationChannel
import android.app.NotificationManager
import android.os.Build
import com.google.firebase.messaging.FirebaseMessaging
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.SupervisorJob
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch
import ph.queenscup.customer.data.api.ApiClient
import ph.queenscup.customer.data.local.SessionStore
import ph.queenscup.customer.data.repository.ReservationRepository

class QueensCupApp : Application() {

    lateinit var session: SessionStore
        private set

    lateinit var repository: ReservationRepository
        private set

    private val scope = CoroutineScope(SupervisorJob())

    override fun onCreate() {
        super.onCreate()

        session = SessionStore(this)
        repository = ReservationRepository(session)

        createNotificationChannel()

        // Retrofit reads the token on every call, so signing in or out takes
        // effect straight away without rebuilding the client.
        var cachedToken: String? = null
        scope.launch { session.token.collect { cachedToken = it } }
        ApiClient.useTokenProvider { cachedToken }

        registerForPush()
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.O) return

        val channel = NotificationChannel(
            CHANNEL_ID,
            "Reservation updates",
            NotificationManager.IMPORTANCE_HIGH,
        ).apply {
            description = "Tells you when your drinks are ready for pick up."
        }

        getSystemService(NotificationManager::class.java).createNotificationChannel(channel)
    }

    /**
     * Sends the current FCM token to the server on every launch. Firebase can
     * rotate tokens at any time, and re-registering is idempotent server side,
     * so this is safe to repeat.
     *
     * Failing here is not fatal: without Firebase configured the app still
     * shows live status whenever the Track tab refreshes.
     */
    private fun registerForPush() {
        runCatching {
            FirebaseMessaging.getInstance().token.addOnSuccessListener { token ->
                scope.launch {
                    val reference = session.references.first().lastOrNull()
                    repository.registerDevice(token, reference)
                }
            }
        }
    }

    companion object {
        const val CHANNEL_ID = "reservation_status"
    }
}

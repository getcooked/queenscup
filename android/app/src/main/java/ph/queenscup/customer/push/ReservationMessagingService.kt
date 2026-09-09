package ph.queenscup.customer.push

import android.Manifest
import android.app.PendingIntent
import android.content.Intent
import android.content.pm.PackageManager
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat
import androidx.core.app.NotificationManagerCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import ph.queenscup.customer.MainActivity
import ph.queenscup.customer.QueensCupApp
import ph.queenscup.customer.R

/**
 * Receives "your order is ready" pushes from Laravel.
 *
 * Tapping the notification opens the app straight onto the Track tab for that
 * reservation, which is why the reference travels in the data payload.
 */
class ReservationMessagingService : FirebaseMessagingService() {

    override fun onNewToken(token: String) {
        val app = application as? QueensCupApp ?: return

        CoroutineScope(Dispatchers.IO).launch {
            app.repository.registerDevice(token)
        }
    }

    override fun onMessageReceived(message: RemoteMessage) {
        val reference = message.data["reference"]
        val title = message.notification?.title ?: "Queen's Cup"
        val body = message.notification?.body
            ?: reference?.let { "Reservation $it is ready for pick up." }
            ?: "Your reservation has been updated."

        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_SINGLE_TOP
            putExtra(MainActivity.EXTRA_REFERENCE, reference)
        }

        val pending = PendingIntent.getActivity(
            this,
            reference?.hashCode() ?: 0,
            intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE,
        )

        val notification = NotificationCompat.Builder(this, QueensCupApp.CHANNEL_ID)
            .setSmallIcon(R.drawable.ic_notification)
            .setContentTitle(title)
            .setContentText(body)
            .setStyle(NotificationCompat.BigTextStyle().bigText(body))
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setContentIntent(pending)
            .setAutoCancel(true)
            .build()

        // From Android 13 the POST_NOTIFICATIONS permission may be denied; the
        // push is simply dropped rather than crashing the service.
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS)
            != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }

        NotificationManagerCompat.from(this)
            .notify(reference?.hashCode() ?: NOTIFICATION_ID, notification)
    }

    private companion object {
        const val NOTIFICATION_ID = 1001
    }
}

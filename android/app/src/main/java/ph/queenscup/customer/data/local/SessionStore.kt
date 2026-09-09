package ph.queenscup.customer.data.local

import android.content.Context
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.core.stringSetPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore by preferencesDataStore(name = "queenscup")

/**
 * Small persistent store for the auth token, the customer's details and the
 * reservation references made on this device.
 *
 * References are kept locally so a guest who never signs in can still open the
 * Track tab and see the orders they placed from this phone.
 */
/**
 * How the app should be coloured. SYSTEM follows the phone setting, which
 * is what a customer gets until they choose otherwise.
 */
enum class ThemeMode {
    SYSTEM,
    LIGHT,
    DARK;

    companion object {
        fun from(stored: String?): ThemeMode =
            entries.firstOrNull { it.name == stored } ?: SYSTEM
    }
}

class SessionStore(private val context: Context) {

    private object Keys {
        val TOKEN = stringPreferencesKey("auth_token")
        val NAME = stringPreferencesKey("customer_name")
        val CONTACT = stringPreferencesKey("customer_contact")
        val REFERENCES = stringSetPreferencesKey("reservation_references")
        val FCM_TOKEN = stringPreferencesKey("fcm_token")
        val THEME = stringPreferencesKey("theme_mode")
    }

    val token: Flow<String?> = context.dataStore.data.map { it[Keys.TOKEN] }
    val customerName: Flow<String?> = context.dataStore.data.map { it[Keys.NAME] }
    val customerContact: Flow<String?> = context.dataStore.data.map { it[Keys.CONTACT] }
    val fcmToken: Flow<String?> = context.dataStore.data.map { it[Keys.FCM_TOKEN] }

    /** Light, dark, or follow the phone. Defaults to following the phone. */
    val themeMode: Flow<ThemeMode> = context.dataStore.data.map {
        ThemeMode.from(it[Keys.THEME])
    }

    val references: Flow<List<String>> = context.dataStore.data.map {
        it[Keys.REFERENCES].orEmpty().toList()
    }

    suspend fun saveToken(value: String?) = context.dataStore.edit { prefs ->
        if (value == null) prefs.remove(Keys.TOKEN) else prefs[Keys.TOKEN] = value
    }

    suspend fun saveCustomer(name: String, contact: String?) {
        context.dataStore.edit { prefs ->
            prefs[Keys.NAME] = name
            if (contact.isNullOrBlank()) prefs.remove(Keys.CONTACT) else prefs[Keys.CONTACT] = contact
        }
    }

    suspend fun saveThemeMode(mode: ThemeMode) {
        context.dataStore.edit { it[Keys.THEME] = mode.name }
    }

    suspend fun saveFcmToken(value: String) {
        context.dataStore.edit { it[Keys.FCM_TOKEN] = value }
    }

    suspend fun rememberReference(reference: String) {
        context.dataStore.edit { prefs ->
            prefs[Keys.REFERENCES] = prefs[Keys.REFERENCES].orEmpty() + reference
        }
    }

    suspend fun signOut() {
        context.dataStore.edit { prefs -> prefs.remove(Keys.TOKEN) }
    }
}

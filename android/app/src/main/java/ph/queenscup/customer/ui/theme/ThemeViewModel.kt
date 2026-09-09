package ph.queenscup.customer.ui.theme

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch
import ph.queenscup.customer.data.local.SessionStore
import ph.queenscup.customer.data.local.ThemeMode

/**
 * Holds the chosen appearance.
 *
 * Read straight from the store as a StateFlow so the whole app recolours the
 * moment the choice changes, and the choice survives a restart.
 */
class ThemeViewModel(app: Application) : AndroidViewModel(app) {

    private val session = SessionStore(app)

    val mode: StateFlow<ThemeMode> = session.themeMode.stateIn(
        scope = viewModelScope,
        started = SharingStarted.WhileSubscribed(5_000),
        initialValue = ThemeMode.SYSTEM,
    )

    fun choose(mode: ThemeMode) {
        viewModelScope.launch { session.saveThemeMode(mode) }
    }
}

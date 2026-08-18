package ph.queenscup.customer.ui.track

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.isActive
import kotlinx.coroutines.launch
import ph.queenscup.customer.QueensCupApp
import ph.queenscup.customer.data.model.Reservation
import ph.queenscup.customer.ui.friendlyMessage

data class TrackUiState(
    val reservations: List<Reservation> = emptyList(),
    val lookup: String = "",
    val loading: Boolean = false,
    val error: String? = null,
)

class TrackViewModel(app: Application) : AndroidViewModel(app) {

    private val repository = (app as QueensCupApp).repository

    private val _state = MutableStateFlow(TrackUiState())
    val state: StateFlow<TrackUiState> = _state.asStateFlow()

    init {
        refresh()
        pollWhileActive()
    }

    /**
     * Push is the primary way a customer hears their drinks are ready, but it
     * needs Firebase configured and notification permission granted. Polling
     * every 20 seconds means the screen is still correct without either.
     */
    private fun pollWhileActive() {
        viewModelScope.launch {
            while (isActive) {
                delay(20_000)
                if (_state.value.reservations.any { !it.isFinished }) refresh(quiet = true)
            }
        }
    }

    fun refresh(quiet: Boolean = false) {
        viewModelScope.launch {
            if (!quiet) _state.update { it.copy(loading = true, error = null) }

            runCatching { repository.localReservations() }
                .onSuccess { list -> _state.update { it.copy(reservations = list, loading = false) } }
                .onFailure { error ->
                    _state.update {
                        it.copy(loading = false, error = if (quiet) it.error else error.friendlyMessage())
                    }
                }
        }
    }

    fun setLookup(value: String) = _state.update { it.copy(lookup = value) }

    /** Looks up a reference typed in by hand, e.g. on a different phone. */
    fun lookup() {
        val reference = _state.value.lookup.trim()
        if (reference.isBlank()) return

        viewModelScope.launch {
            _state.update { it.copy(loading = true, error = null) }

            runCatching { repository.track(reference) }
                .onSuccess { reservation ->
                    _state.update { current ->
                        val existing = current.reservations.filterNot { it.reference == reservation.reference }
                        current.copy(
                            reservations = listOf(reservation) + existing,
                            loading = false,
                            lookup = "",
                        )
                    }
                }
                .onFailure { error ->
                    _state.update {
                        it.copy(loading = false, error = "No reservation found with that code.")
                    }
                }
        }
    }

    fun cancel(reference: String) {
        viewModelScope.launch {
            runCatching { repository.cancel(reference) }
                .onSuccess { refresh(quiet = true) }
                .onFailure { error -> _state.update { it.copy(error = error.friendlyMessage()) } }
        }
    }
}

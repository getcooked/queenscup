package ph.queenscup.customer.data.repository

import kotlinx.coroutines.flow.first
import ph.queenscup.customer.data.api.ApiClient
import ph.queenscup.customer.data.local.SessionStore
import ph.queenscup.customer.data.model.BasketLine
import ph.queenscup.customer.data.model.DeviceTokenRequest
import ph.queenscup.customer.data.model.Product
import ph.queenscup.customer.data.model.QuoteRequest
import ph.queenscup.customer.data.model.QuoteResponse
import ph.queenscup.customer.data.model.Reservation
import ph.queenscup.customer.data.model.ReservationRequest
import ph.queenscup.customer.data.model.ServiceType

/**
 * Everything the UI needs, with the network details kept out of the screens.
 *
 * Money is never computed here. The basket is sent to the server, which prices
 * it and returns the totals, so the app and the counter can never disagree on
 * what a reservation costs.
 */
class ReservationRepository(private val session: SessionStore) {

    private val api = ApiClient.service

    suspend fun products(): List<Product> = api.products().data

    suspend fun quote(lines: List<BasketLine>, serviceType: ServiceType): QuoteResponse =
        api.quote(QuoteRequest(serviceType.wire, lines))

    suspend fun reserve(
        lines: List<BasketLine>,
        serviceType: ServiceType,
        name: String,
        contact: String?,
        notes: String?,
    ): Reservation {
        val reservation = api.reserve(
            ReservationRequest(
                serviceType = serviceType.wire,
                customerName = name,
                customerContact = contact,
                notes = notes,
                deviceToken = session.fcmToken.first(),
                items = lines,
            )
        )

        session.saveCustomer(name, contact)
        session.rememberReference(reservation.reference)

        return reservation
    }

    suspend fun track(reference: String): Reservation = api.track(reference.trim().uppercase())

    suspend fun cancel(reference: String): Reservation = api.cancel(reference.trim().uppercase())

    /**
     * Reservations placed from this device, newest first. Any reference the
     * server no longer recognises is skipped rather than failing the screen.
     */
    suspend fun localReservations(): List<Reservation> =
        session.references.first()
            .mapNotNull { reference -> runCatching { api.track(reference) }.getOrNull() }
            .sortedByDescending { it.placedAt }

    suspend fun registerDevice(token: String, reference: String? = null) {
        session.saveFcmToken(token)
        runCatching {
            api.registerDevice(DeviceTokenRequest(token = token, reservationReference = reference))
        }
    }
}

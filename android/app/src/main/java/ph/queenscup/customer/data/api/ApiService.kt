package ph.queenscup.customer.data.api

import ph.queenscup.customer.data.model.AuthRequest
import ph.queenscup.customer.data.model.AuthResponse
import ph.queenscup.customer.data.model.DeviceTokenRequest
import ph.queenscup.customer.data.model.ProductsResponse
import ph.queenscup.customer.data.model.QuoteRequest
import ph.queenscup.customer.data.model.QuoteResponse
import ph.queenscup.customer.data.model.Reservation
import ph.queenscup.customer.data.model.ReservationListResponse
import ph.queenscup.customer.data.model.ReservationRequest
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path

interface ApiService {

    @GET("products")
    suspend fun products(): ProductsResponse

    /** Prices a basket without saving it, so the cup fee is visible up front. */
    @POST("reservations/quote")
    suspend fun quote(@Body body: QuoteRequest): QuoteResponse

    @POST("reservations")
    suspend fun reserve(@Body body: ReservationRequest): Reservation

    @GET("reservations/{reference}")
    suspend fun track(@Path("reference") reference: String): Reservation

    @POST("reservations/{reference}/cancel")
    suspend fun cancel(@Path("reference") reference: String): Reservation

    @GET("my/reservations")
    suspend fun myReservations(): ReservationListResponse

    @POST("device-tokens")
    suspend fun registerDevice(@Body body: DeviceTokenRequest)

    @POST("auth/login")
    suspend fun login(@Body body: AuthRequest): AuthResponse

    @POST("auth/register")
    suspend fun register(@Body body: AuthRequest): AuthResponse
}

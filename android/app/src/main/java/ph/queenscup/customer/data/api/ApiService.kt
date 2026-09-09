package ph.queenscup.customer.data.api

import ph.queenscup.customer.data.model.AuthRequest
import ph.queenscup.customer.data.model.AuthResponse
import ph.queenscup.customer.data.model.AuthUser
import ph.queenscup.customer.data.model.ChatHistory
import ph.queenscup.customer.data.model.ChatReply
import ph.queenscup.customer.data.model.ChatRequest
import ph.queenscup.customer.data.model.DeviceTokenRequest
import ph.queenscup.customer.data.model.EmailRequest
import ph.queenscup.customer.data.model.VerificationStarted
import ph.queenscup.customer.data.model.VerifyRequest
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

    // Sign up sends a code and waits; the token only arrives once the
    // address is confirmed, exactly as on the website.
    @POST("auth/register")
    suspend fun register(@Body body: AuthRequest): VerificationStarted

    @POST("auth/verify")
    suspend fun verify(@Body body: VerifyRequest): AuthResponse

    @POST("auth/resend")
    suspend fun resendCode(@Body body: EmailRequest)

    @GET("auth/me")
    suspend fun me(): AuthUser

    @POST("auth/logout")
    suspend fun logout()

    // The assistant. Works signed out too, it just is not kept then.
    @GET("chat")
    suspend fun chatHistory(): ChatHistory

    @POST("chat")
    suspend fun chatSend(@Body body: ChatRequest): ChatReply
}

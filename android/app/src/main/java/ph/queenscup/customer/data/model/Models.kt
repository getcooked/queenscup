package ph.queenscup.customer.data.model

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class Product(
    val id: Long,
    val name: String,
    val category: String? = null,
    @SerialName("regular_price") val regularPrice: Double,
    @SerialName("large_price") val largePrice: Double,
    val stock: Int = 0,
    val available: Boolean = true,
    val description: String? = null,
    @SerialName("image_url") val imageUrl: String? = null,
) {
    fun priceFor(size: CupSize): Double = when (size) {
        CupSize.REGULAR -> regularPrice
        CupSize.LARGE -> largePrice
    }
}

@Serializable
data class ProductsResponse(
    val data: List<Product>,
    val categories: List<String> = emptyList(),
    @SerialName("takeout_fee_per_cup") val takeoutFeePerCup: Double = 5.0,
)

enum class CupSize(val wire: String, val label: String) {
    REGULAR("regular", "16oz"),
    LARGE("large", "22oz");

    companion object {
        fun from(wire: String?) = entries.firstOrNull { it.wire == wire } ?: REGULAR
    }
}

/**
 * Dine-in is served in-store; take-out is charged a fee per cup, which the
 * server calculates. The app never adds the fee itself, it only displays what
 * the quote endpoint returns.
 */
enum class ServiceType(val wire: String, val label: String) {
    DINE_IN("dine_in", "Dine in"),
    TAKE_OUT("take_out", "Take out");

    companion object {
        fun from(wire: String?) = entries.firstOrNull { it.wire == wire } ?: DINE_IN
    }
}

@Serializable
data class BasketLine(
    @SerialName("inventory_id") val inventoryId: Long,
    val size: String,
    val quantity: Int,
)

@Serializable
data class QuoteRequest(
    @SerialName("service_type") val serviceType: String,
    val items: List<BasketLine>,
)

@Serializable
data class QuoteResponse(
    @SerialName("service_type") val serviceType: String,
    @SerialName("cup_count") val cupCount: Int,
    val subtotal: Double,
    @SerialName("takeout_fee") val takeoutFee: Double,
    @SerialName("takeout_fee_per_cup") val takeoutFeePerCup: Double = 5.0,
    val total: Double,
)

@Serializable
data class ReservationRequest(
    @SerialName("service_type") val serviceType: String,
    @SerialName("customer_name") val customerName: String,
    @SerialName("customer_contact") val customerContact: String? = null,
    @SerialName("customer_email") val customerEmail: String? = null,
    val notes: String? = null,
    val source: String = "android",
    @SerialName("device_token") val deviceToken: String? = null,
    val items: List<BasketLine>,
)

@Serializable
data class ReservationItem(
    val name: String,
    val size: String,
    @SerialName("size_label") val sizeLabel: String,
    @SerialName("unit_price") val unitPrice: Double,
    val quantity: Int,
    @SerialName("line_total") val lineTotal: Double,
)

@Serializable
data class Reservation(
    val reference: String,
    @SerialName("customer_name") val customerName: String,
    val branch: String? = null,
    @SerialName("service_type") val serviceType: String,
    val status: String,
    @SerialName("status_label") val statusLabel: String,
    @SerialName("cup_count") val cupCount: Int,
    val subtotal: Double,
    @SerialName("takeout_fee") val takeoutFee: Double,
    val total: Double,
    @SerialName("payment_method") val paymentMethod: String? = null,
    @SerialName("payment_status") val paymentStatus: String,
    @SerialName("placed_at") val placedAt: String? = null,
    @SerialName("ready_at") val readyAt: String? = null,
    val items: List<ReservationItem> = emptyList(),
) {
    val isReady: Boolean get() = status == "ready"
    val isFinished: Boolean get() = status == "completed" || status == "cancelled"

    /** Position along pending -> preparing -> ready -> completed, for the tracker. */
    val stepIndex: Int
        get() = when (status) {
            "pending" -> 0
            "preparing" -> 1
            "ready" -> 2
            "completed" -> 3
            else -> -1
        }
}

@Serializable
data class ReservationListResponse(val data: List<Reservation>)

@Serializable
data class DeviceTokenRequest(
    val token: String,
    val platform: String = "android",
    @SerialName("reservation_reference") val reservationReference: String? = null,
)

@Serializable
data class AuthRequest(
    val name: String? = null,
    val email: String,
    val password: String,
    @SerialName("contact_number") val contactNumber: String? = null,
    @SerialName("device_name") val deviceName: String = "android",
)

/** Confirming the six digit code that was emailed on sign up. */
@Serializable
data class VerifyRequest(
    val email: String,
    val code: String,
    @SerialName("device_name") val deviceName: String = "android",
)

@Serializable
data class EmailRequest(val email: String)

/**
 * Sign up does not hand back a token: the address has to be confirmed
 * first, so only status and email come back at that point.
 */
@Serializable
data class VerificationStarted(
    val status: String,
    val email: String,
    val message: String? = null,
)

@Serializable
data class ChatMessagePayload(
    val author: String,
    val body: String,
    @SerialName("quick_replies") val quickReplies: List<String> = emptyList(),
)

@Serializable
data class ChatHistory(val data: List<ChatMessagePayload> = emptyList(), val stored: Boolean = false)

@Serializable
data class ChatRequest(val message: String)

@Serializable
data class ChatReply(
    val body: String,
    @SerialName("quick_replies") val quickReplies: List<String> = emptyList(),
    val stored: Boolean = false,
)

@Serializable
data class AuthUser(
    val id: Long,
    @SerialName("fullName") val fullName: String,
    val email: String,
    @SerialName("contactNumber") val contactNumber: String? = null,
    val role: String,
)

@Serializable
data class AuthResponse(val status: String? = null, val token: String, val user: AuthUser)

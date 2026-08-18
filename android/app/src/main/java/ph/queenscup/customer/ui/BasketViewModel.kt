package ph.queenscup.customer.ui

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.Job
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import ph.queenscup.customer.QueensCupApp
import ph.queenscup.customer.data.model.BasketLine
import ph.queenscup.customer.data.model.CupSize
import ph.queenscup.customer.data.model.Product
import ph.queenscup.customer.data.model.QuoteResponse
import ph.queenscup.customer.data.model.Reservation
import ph.queenscup.customer.data.model.ServiceType

/** One line in the basket: a drink at a size, with a quantity. */
data class BasketEntry(
    val product: Product,
    val size: CupSize,
    val quantity: Int,
) {
    val key: String get() = "${product.id}-${size.wire}"
    val lineTotal: Double get() = product.priceFor(size) * quantity
}

data class BasketUiState(
    val products: List<Product> = emptyList(),
    val categories: List<String> = emptyList(),
    val selectedCategory: String? = null,
    val entries: List<BasketEntry> = emptyList(),
    val serviceType: ServiceType = ServiceType.DINE_IN,
    val quote: QuoteResponse? = null,
    val takeoutFeePerCup: Double = 5.0,
    val loadingMenu: Boolean = false,
    val quoting: Boolean = false,
    val submitting: Boolean = false,
    val error: String? = null,
    val placed: Reservation? = null,
    val customerName: String = "",
    val customerContact: String = "",
) {
    val cupCount: Int get() = entries.sumOf { it.quantity }
    val isEmpty: Boolean get() = entries.isEmpty()

    /**
     * Shown only until the server quote arrives. The authoritative figures
     * always come from the API so the app can never disagree with the counter.
     */
    val estimatedSubtotal: Double get() = entries.sumOf { it.lineTotal }

    val visibleProducts: List<Product>
        get() = selectedCategory?.let { category -> products.filter { it.category == category } } ?: products
}

class BasketViewModel(app: Application) : AndroidViewModel(app) {

    private val repository = (app as QueensCupApp).repository
    private val session = (app as QueensCupApp).session

    private val _state = MutableStateFlow(BasketUiState())
    val state: StateFlow<BasketUiState> = _state.asStateFlow()

    private var quoteJob: Job? = null

    init {
        loadMenu()
        viewModelScope.launch {
            _state.update {
                it.copy(
                    customerName = session.customerName.first().orEmpty(),
                    customerContact = session.customerContact.first().orEmpty(),
                )
            }
        }
    }

    fun loadMenu() {
        viewModelScope.launch {
            _state.update { it.copy(loadingMenu = true, error = null) }
            runCatching { repository.products() }
                .onSuccess { products ->
                    _state.update {
                        it.copy(
                            products = products,
                            categories = products.mapNotNull(Product::category).distinct(),
                            loadingMenu = false,
                        )
                    }
                }
                .onFailure { error ->
                    _state.update {
                        it.copy(loadingMenu = false, error = error.friendlyMessage())
                    }
                }
        }
    }

    fun selectCategory(category: String?) = _state.update { it.copy(selectedCategory = category) }

    fun add(product: Product, size: CupSize) {
        _state.update { current ->
            val key = "${product.id}-${size.wire}"
            val existing = current.entries.firstOrNull { it.key == key }
            val entries = if (existing == null) {
                current.entries + BasketEntry(product, size, 1)
            } else {
                current.entries.map { if (it.key == key) it.copy(quantity = it.quantity + 1) else it }
            }
            current.copy(entries = entries)
        }
        refreshQuote()
    }

    fun changeQuantity(key: String, delta: Int) {
        _state.update { current ->
            val entries = current.entries.mapNotNull { entry ->
                if (entry.key != key) entry
                else (entry.quantity + delta).let { if (it <= 0) null else entry.copy(quantity = it) }
            }
            current.copy(entries = entries)
        }
        refreshQuote()
    }

    fun remove(key: String) {
        _state.update { it.copy(entries = it.entries.filterNot { entry -> entry.key == key }) }
        refreshQuote()
    }

    fun setServiceType(serviceType: ServiceType) {
        _state.update { it.copy(serviceType = serviceType) }
        refreshQuote()
    }

    fun setCustomerName(value: String) = _state.update { it.copy(customerName = value) }

    fun setCustomerContact(value: String) = _state.update { it.copy(customerContact = value) }

    /**
     * Re-prices the basket server side after every change, so the take-out
     * surcharge shown is exactly the one that will be charged.
     */
    private fun refreshQuote() {
        quoteJob?.cancel()

        val current = _state.value
        if (current.isEmpty) {
            _state.update { it.copy(quote = null) }
            return
        }

        quoteJob = viewModelScope.launch {
            _state.update { it.copy(quoting = true) }
            runCatching { repository.quote(current.toLines(), current.serviceType) }
                .onSuccess { quote ->
                    _state.update {
                        it.copy(quote = quote, quoting = false, takeoutFeePerCup = quote.takeoutFeePerCup)
                    }
                }
                .onFailure { error ->
                    _state.update { it.copy(quoting = false, error = error.friendlyMessage()) }
                }
        }
    }

    fun submit(notes: String?) {
        val current = _state.value

        if (current.isEmpty) return
        if (current.customerName.isBlank()) {
            _state.update { it.copy(error = "Please tell us the name for this reservation.") }
            return
        }

        viewModelScope.launch {
            _state.update { it.copy(submitting = true, error = null) }
            runCatching {
                repository.reserve(
                    lines = current.toLines(),
                    serviceType = current.serviceType,
                    name = current.customerName.trim(),
                    contact = current.customerContact.trim().ifBlank { null },
                    notes = notes?.ifBlank { null },
                )
            }
                .onSuccess { reservation ->
                    _state.update {
                        it.copy(submitting = false, placed = reservation, entries = emptyList(), quote = null)
                    }
                }
                .onFailure { error ->
                    _state.update { it.copy(submitting = false, error = error.friendlyMessage()) }
                }
        }
    }

    fun consumePlaced() = _state.update { it.copy(placed = null) }

    fun clearError() = _state.update { it.copy(error = null) }

    private fun BasketUiState.toLines(): List<BasketLine> = entries.map {
        BasketLine(inventoryId = it.product.id, size = it.size.wire, quantity = it.quantity)
    }
}

internal fun Throwable.friendlyMessage(): String = when (this) {
    is java.net.UnknownHostException, is java.net.ConnectException ->
        "Can't reach Queen's Cup. Check your connection and try again."
    is java.net.SocketTimeoutException -> "That took too long. Please try again."
    else -> message ?: "Something went wrong. Please try again."
}

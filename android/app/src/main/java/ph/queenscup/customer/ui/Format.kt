package ph.queenscup.customer.ui

import java.util.Locale

/** Formats an amount as Philippine pesos, e.g. 1234.5 -> "P1,234.50". */
fun peso(amount: Double): String = "₱" + String.format(Locale.US, "%,.2f", amount)

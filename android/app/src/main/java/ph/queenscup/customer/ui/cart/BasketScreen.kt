package ph.queenscup.customer.ui.cart

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.Divider
import androidx.compose.material3.FilterChip
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import ph.queenscup.customer.data.model.ServiceType
import ph.queenscup.customer.ui.BasketViewModel
import ph.queenscup.customer.ui.peso

@Composable
fun BasketScreen(
    viewModel: BasketViewModel,
    onBrowseMenu: () -> Unit,
    onTrackReservation: () -> Unit,
) {
    val state by viewModel.state.collectAsStateWithLifecycle()
    var notes by remember { mutableStateOf("") }

    state.placed?.let { reservation ->
        AlertDialog(
            onDismissRequest = { viewModel.consumePlaced() },
            title = { Text("Reservation confirmed") },
            text = {
                Column {
                    Text("Show this code at the counter:")
                    Spacer(Modifier.height(8.dp))
                    Text(
                        reservation.reference,
                        style = MaterialTheme.typography.headlineSmall,
                        color = MaterialTheme.colorScheme.primary,
                    )
                    Spacer(Modifier.height(12.dp))
                    Text("Total due on pick up: ${peso(reservation.total)}")
                    Text(
                        "Pay with cash, GCash or PayMaya at the counter.",
                        style = MaterialTheme.typography.labelSmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                    )
                }
            },
            confirmButton = {
                TextButton(onClick = {
                    viewModel.consumePlaced()
                    onTrackReservation()
                }) { Text("Track it") }
            },
            dismissButton = {
                TextButton(onClick = { viewModel.consumePlaced() }) { Text("Done") }
            },
        )
    }

    if (state.isEmpty) {
        EmptyBasket(onBrowseMenu)
        return
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
    ) {
        Text("Your reservation", style = MaterialTheme.typography.headlineSmall)
        Spacer(Modifier.height(16.dp))

        // ---- How they want it served -------------------------------------
        Text("How will you have it?", style = MaterialTheme.typography.titleMedium)
        Spacer(Modifier.height(8.dp))

        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            ServiceType.entries.forEach { type ->
                FilterChip(
                    selected = state.serviceType == type,
                    onClick = { viewModel.setServiceType(type) },
                    label = { Text(type.label) },
                )
            }
        }

        if (state.serviceType == ServiceType.TAKE_OUT) {
            Spacer(Modifier.height(6.dp))
            Text(
                "Take out adds ${peso(state.takeoutFeePerCup)} per cup for the cup and lid.",
                style = MaterialTheme.typography.labelSmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
        }

        Spacer(Modifier.height(16.dp))

        // ---- Lines --------------------------------------------------------
        Card {
            Column(Modifier.padding(12.dp)) {
                state.entries.forEachIndexed { index, entry ->
                    if (index > 0) Divider(Modifier.padding(vertical = 8.dp))

                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Column(Modifier.weight(1f)) {
                            Text(entry.product.name, style = MaterialTheme.typography.titleMedium)
                            Text(
                                "${entry.size.label} · ${peso(entry.product.priceFor(entry.size))}",
                                style = MaterialTheme.typography.labelSmall,
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                            )
                        }

                        IconButton(onClick = { viewModel.changeQuantity(entry.key, -1) }) { Text("−") }
                        Text(entry.quantity.toString(), fontWeight = FontWeight.Bold)
                        IconButton(onClick = { viewModel.changeQuantity(entry.key, +1) }) { Text("+") }

                        Text(peso(entry.lineTotal), fontWeight = FontWeight.SemiBold)
                    }
                }
            }
        }

        Spacer(Modifier.height(16.dp))

        // ---- Totals, straight from the server -----------------------------
        Card {
            Column(Modifier.padding(12.dp)) {
                val quote = state.quote

                TotalRow("Subtotal", peso(quote?.subtotal ?: state.estimatedSubtotal))

                if (state.serviceType == ServiceType.TAKE_OUT) {
                    TotalRow(
                        "Take-out cups (${state.cupCount} × ${peso(state.takeoutFeePerCup)})",
                        peso(quote?.takeoutFee ?: 0.0),
                    )
                }

                Divider(Modifier.padding(vertical = 8.dp))

                Row(Modifier.fillMaxWidth()) {
                    Text("Total", Modifier.weight(1f), fontWeight = FontWeight.Bold)
                    Text(
                        peso(quote?.total ?: state.estimatedSubtotal),
                        fontWeight = FontWeight.Bold,
                        color = MaterialTheme.colorScheme.primary,
                    )
                }

                if (state.quoting) {
                    Text(
                        "Updating total…",
                        style = MaterialTheme.typography.labelSmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                    )
                }
            }
        }

        Spacer(Modifier.height(16.dp))

        // ---- Who it is for ------------------------------------------------
        OutlinedTextField(
            value = state.customerName,
            onValueChange = viewModel::setCustomerName,
            label = { Text("Name for the reservation") },
            singleLine = true,
            modifier = Modifier.fillMaxWidth(),
        )
        Spacer(Modifier.height(8.dp))
        OutlinedTextField(
            value = state.customerContact,
            onValueChange = viewModel::setCustomerContact,
            label = { Text("Contact number (optional)") },
            singleLine = true,
            modifier = Modifier.fillMaxWidth(),
        )
        Spacer(Modifier.height(8.dp))
        OutlinedTextField(
            value = notes,
            onValueChange = { notes = it },
            label = { Text("Notes for the barista (optional)") },
            modifier = Modifier.fillMaxWidth(),
        )

        state.error?.let { error ->
            Spacer(Modifier.height(12.dp))
            Text(error, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodyMedium)
        }

        Spacer(Modifier.height(16.dp))

        Button(
            onClick = { viewModel.submit(notes) },
            enabled = !state.submitting && !state.isEmpty,
            modifier = Modifier.fillMaxWidth(),
        ) {
            Text(if (state.submitting) "Reserving…" else "Confirm reservation")
        }

        Spacer(Modifier.height(8.dp))
        Text(
            "Nothing is charged now. Pay at the counter when you pick up.",
            style = MaterialTheme.typography.labelSmall,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
        )
        Spacer(Modifier.height(24.dp))
    }
}

@Composable
private fun TotalRow(label: String, value: String) {
    Row(Modifier.fillMaxWidth().padding(vertical = 2.dp)) {
        Text(label, Modifier.weight(1f), style = MaterialTheme.typography.bodyMedium)
        Text(value, style = MaterialTheme.typography.bodyMedium)
    }
}

@Composable
private fun EmptyBasket(onBrowseMenu: () -> Unit) {
    Column(
        modifier = Modifier.fillMaxSize().padding(32.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Text("No drinks yet", style = MaterialTheme.typography.titleMedium)
        Spacer(Modifier.height(8.dp))
        Text(
            "Pick something from the menu and it will show up here.",
            style = MaterialTheme.typography.bodyMedium,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
        )
        Spacer(Modifier.height(16.dp))
        Button(onClick = onBrowseMenu) { Text("Browse the menu") }
    }
}

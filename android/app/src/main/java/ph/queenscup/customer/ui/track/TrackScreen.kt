package ph.queenscup.customer.ui.track

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.Divider
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewmodel.compose.viewModel
import ph.queenscup.customer.data.model.Reservation
import ph.queenscup.customer.data.model.ServiceType
import ph.queenscup.customer.ui.peso

private val STEPS = listOf("Reserved", "Preparing", "Ready", "Picked up")

@Composable
fun TrackScreen(
    initialReference: String? = null,
    viewModel: TrackViewModel = viewModel(),
) {
    val state by viewModel.state.collectAsStateWithLifecycle()

    // Arriving from a push notification: pull that reservation up immediately.
    LaunchedEffect(initialReference) {
        if (!initialReference.isNullOrBlank()) {
            viewModel.setLookup(initialReference)
            viewModel.lookup()
        }
    }

    Column(Modifier.fillMaxSize().padding(16.dp)) {
        Text("Track your reservation", style = MaterialTheme.typography.headlineSmall)
        Spacer(Modifier.height(12.dp))

        Row(verticalAlignment = Alignment.CenterVertically) {
            OutlinedTextField(
                value = state.lookup,
                onValueChange = viewModel::setLookup,
                label = { Text("Reference code") },
                placeholder = { Text("QC-XXXXXX") },
                singleLine = true,
                modifier = Modifier.weight(1f),
            )
            Spacer(Modifier.size(8.dp))
            Button(onClick = viewModel::lookup, enabled = state.lookup.isNotBlank()) { Text("Find") }
        }

        state.error?.let {
            Spacer(Modifier.height(8.dp))
            Text(it, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodyMedium)
        }

        Spacer(Modifier.height(16.dp))

        if (state.reservations.isEmpty() && !state.loading) {
            Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text(
                    "Reservations you make will appear here.",
                    style = MaterialTheme.typography.bodyMedium,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                items(state.reservations, key = { it.reference }) { reservation ->
                    ReservationCard(
                        reservation = reservation,
                        onCancel = { viewModel.cancel(reservation.reference) },
                    )
                }
            }
        }
    }
}

@Composable
private fun ReservationCard(reservation: Reservation, onCancel: () -> Unit) {
    Card {
        Column(Modifier.padding(14.dp)) {

            Row(verticalAlignment = Alignment.CenterVertically) {
                Column(Modifier.weight(1f)) {
                    Text(
                        reservation.reference,
                        style = MaterialTheme.typography.titleMedium,
                        color = MaterialTheme.colorScheme.primary,
                    )
                    Text(
                        ServiceType.from(reservation.serviceType).label +
                            " · ${reservation.cupCount} ${if (reservation.cupCount == 1) "cup" else "cups"}",
                        style = MaterialTheme.typography.labelSmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                    )
                }

                Text(
                    reservation.statusLabel,
                    style = MaterialTheme.typography.labelSmall,
                    fontWeight = FontWeight.Bold,
                    color = if (reservation.isReady) {
                        MaterialTheme.colorScheme.primary
                    } else {
                        MaterialTheme.colorScheme.onSurfaceVariant
                    },
                )
            }

            Spacer(Modifier.height(12.dp))

            if (reservation.status != "cancelled") {
                StatusTrail(reservation.stepIndex)
                Spacer(Modifier.height(12.dp))
            }

            reservation.items.forEach { item ->
                Row(Modifier.fillMaxWidth().padding(vertical = 1.dp)) {
                    Text(
                        "${item.quantity}× ${item.name} (${item.sizeLabel})",
                        Modifier.weight(1f),
                        style = MaterialTheme.typography.bodyMedium,
                    )
                    Text(peso(item.lineTotal), style = MaterialTheme.typography.bodyMedium)
                }
            }

            if (reservation.takeoutFee > 0) {
                Row(Modifier.fillMaxWidth().padding(vertical = 1.dp)) {
                    Text("Take-out cups", Modifier.weight(1f), style = MaterialTheme.typography.bodyMedium)
                    Text(peso(reservation.takeoutFee), style = MaterialTheme.typography.bodyMedium)
                }
            }

            Divider(Modifier.padding(vertical = 8.dp))

            Row(Modifier.fillMaxWidth()) {
                Text("Total", Modifier.weight(1f), fontWeight = FontWeight.Bold)
                Text(peso(reservation.total), fontWeight = FontWeight.Bold)
            }

            Text(
                if (reservation.paymentStatus == "paid") {
                    "Paid via ${reservation.paymentMethod?.uppercase() ?: "counter"}"
                } else {
                    "Pay at the counter on pick up"
                },
                style = MaterialTheme.typography.labelSmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )

            // Only an untouched reservation can be called off from the phone.
            if (reservation.status == "pending") {
                TextButton(onClick = onCancel) { Text("Cancel reservation") }
            }
        }
    }
}

/** Four dots showing how far along the counter is. */
@Composable
private fun StatusTrail(stepIndex: Int) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        STEPS.forEachIndexed { index, label ->
            val reached = index <= stepIndex

            Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.weight(1f)) {
                Box(
                    Modifier
                        .size(12.dp)
                        .clip(CircleShape)
                        .background(
                            if (reached) MaterialTheme.colorScheme.primary
                            else MaterialTheme.colorScheme.surfaceVariant
                        )
                )
                Spacer(Modifier.height(4.dp))
                Text(
                    label,
                    style = MaterialTheme.typography.labelSmall,
                    color = if (reached) {
                        MaterialTheme.colorScheme.primary
                    } else {
                        MaterialTheme.colorScheme.onSurfaceVariant
                    },
                )
            }
        }
    }
}

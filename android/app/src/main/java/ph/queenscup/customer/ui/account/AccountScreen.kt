package ph.queenscup.customer.ui.account

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.Card
import androidx.compose.material3.Divider
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import ph.queenscup.customer.ui.BasketViewModel
import ph.queenscup.customer.ui.peso

@Composable
fun AccountScreen(viewModel: BasketViewModel) {
    val state by viewModel.state.collectAsStateWithLifecycle()

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
    ) {
        Text("Account", style = MaterialTheme.typography.headlineSmall)
        Spacer(Modifier.height(16.dp))

        Card {
            Column(Modifier.padding(14.dp)) {
                Text("Reserving as", style = MaterialTheme.typography.labelSmall)
                Text(
                    state.customerName.ifBlank { "Guest" },
                    style = MaterialTheme.typography.titleMedium,
                )
                if (state.customerContact.isNotBlank()) {
                    Text(state.customerContact, style = MaterialTheme.typography.bodyMedium)
                }
                Spacer(Modifier.height(8.dp))
                Text(
                    "You don't need an account. Your name is saved on this phone and " +
                        "your reservations are tracked by their reference code.",
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }
        }

        Spacer(Modifier.height(16.dp))

        Card {
            Column(Modifier.padding(14.dp)) {
                Text("How reserving works", style = MaterialTheme.typography.titleMedium)
                Spacer(Modifier.height(8.dp))

                Step("1", "Pick your drinks from the menu.")
                Step("2", "Choose dine in, or take out for ${peso(state.takeoutFeePerCup)} per cup.")
                Step("3", "Confirm and keep your reference code.")
                Step("4", "We notify you the moment it is ready.")
                Step("5", "Pay at the counter with cash, GCash or PayMaya.")

                Divider(Modifier.padding(vertical = 12.dp))

                Text(
                    "Nothing is charged in the app. Payment always happens at the counter.",
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }
        }

        Spacer(Modifier.height(16.dp))

        Card {
            Column(Modifier.padding(14.dp)) {
                Text("Queen's Cup Madridejos", style = MaterialTheme.typography.titleMedium)
                Spacer(Modifier.height(4.dp))
                Text("Kota Park — beside the boardwalk", style = MaterialTheme.typography.bodyMedium)
                Text("MCC — inside Madridejos Community College", style = MaterialTheme.typography.bodyMedium)
            }
        }
    }
}

@Composable
private fun Step(number: String, text: String) {
    androidx.compose.foundation.layout.Row(Modifier.fillMaxWidth().padding(vertical = 3.dp)) {
        Text(
            "$number.",
            fontWeight = FontWeight.Bold,
            color = MaterialTheme.colorScheme.primary,
            modifier = Modifier.padding(end = 8.dp),
        )
        Text(text, style = MaterialTheme.typography.bodyMedium)
    }
}

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
import androidx.compose.material3.OutlinedButton
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

/**
 * The account tab.
 *
 * Signed out it is the sign in and sign up flow, matching the website.
 * Signed in it shows the real account rather than a name typed into this
 * phone, which is what lets reservations and chat follow the customer.
 */
@Composable
fun AccountScreen(viewModel: BasketViewModel, authViewModel: AuthViewModel) {
    val state by viewModel.state.collectAsStateWithLifecycle()
    val auth by authViewModel.state.collectAsStateWithLifecycle()

    val account = auth.signedIn
    if (account == null) {
        AuthScreen(viewModel = authViewModel)
        return
    }

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
                Text("Signed in as", style = MaterialTheme.typography.labelSmall)
                Text(account.fullName, style = MaterialTheme.typography.titleMedium)
                Text(account.email, style = MaterialTheme.typography.bodyMedium)
                if (!account.contactNumber.isNullOrBlank()) {
                    Text(account.contactNumber, style = MaterialTheme.typography.bodyMedium)
                }
                Spacer(Modifier.height(10.dp))
                Text(
                    "The same account works on the website, so your reservations " +
                        "and chat are waiting wherever you sign in.",
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
                Spacer(Modifier.height(10.dp))
                OutlinedButton(onClick = { authViewModel.signOut() }) { Text("Sign out") }
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

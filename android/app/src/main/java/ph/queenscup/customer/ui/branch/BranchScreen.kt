package ph.queenscup.customer.ui.branch

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.Card
import androidx.compose.material3.FilterChip
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp

/** The shops a reservation can be picked up from. [wire] is what the API expects. */
enum class Branch(val wire: String, val label: String, val description: String) {
    KOTA_PARK("kotapark", "Kota Park", "Beside the boardwalk, Madridejos"),
    MCC("mcc", "MCC", "Inside Madridejos Community College");

    companion object {
        fun from(wire: String?): Branch? = entries.firstOrNull { it.wire == wire }
    }
}

/**
 * Shown right after signing in, before the menu, so every order is placed
 * with the shop that will actually make it.
 */
@Composable
fun BranchScreen(onChoose: (Branch) -> Unit) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(20.dp),
        verticalArrangement = Arrangement.spacedBy(14.dp),
    ) {
        Text("Choose your branch", style = MaterialTheme.typography.headlineSmall)
        Text(
            "Your drinks are made and picked up here. You can change it later.",
            style = MaterialTheme.typography.bodyMedium,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
        )

        Branch.entries.forEach { branch ->
            Card(
                onClick = { onChoose(branch) },
                modifier = Modifier.fillMaxWidth(),
            ) {
                Column(Modifier.padding(16.dp)) {
                    Text(branch.label, style = MaterialTheme.typography.titleMedium)
                    Text(
                        branch.description,
                        style = MaterialTheme.typography.bodyMedium,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                    )
                }
            }
        }
    }
}

/** Compact switcher for screens where a branch is already chosen. */
@Composable
fun BranchChips(selected: String?, enabled: Boolean = true, onChoose: (Branch) -> Unit) {
    Column {
        Branch.entries.forEach { branch ->
            FilterChip(
                selected = selected == branch.wire,
                onClick = { onChoose(branch) },
                enabled = enabled,
                label = { Text("${branch.label} · ${branch.description}") },
                modifier = Modifier.fillMaxWidth(),
            )
        }
    }
}

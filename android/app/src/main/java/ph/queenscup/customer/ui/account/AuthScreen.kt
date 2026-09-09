package ph.queenscup.customer.ui.account

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.lifecycle.compose.collectAsStateWithLifecycle

/**
 * Signing in, signing up, and confirming the emailed code — the same three
 * steps the website walks a customer through.
 */
@Composable
fun AuthScreen(viewModel: AuthViewModel) {
    val state by viewModel.state.collectAsStateWithLifecycle()

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(20.dp),
        verticalArrangement = Arrangement.spacedBy(14.dp),
    ) {
        Text("The Queen's Cup", style = MaterialTheme.typography.headlineSmall)
        Text(
            when (state.step) {
                AuthStep.SIGN_IN -> "Sign in to reserve and follow your orders."
                AuthStep.REGISTER -> "Create an account to reserve your drinks."
                AuthStep.VERIFY -> "Enter the code we emailed you."
            },
            style = MaterialTheme.typography.bodyMedium,
        )

        state.notice?.let { Notice(it, error = false) }
        state.error?.let { Notice(it, error = true) }

        when (state.step) {
            AuthStep.SIGN_IN -> SignInForm(state, viewModel)
            AuthStep.REGISTER -> RegisterForm(state, viewModel)
            AuthStep.VERIFY -> VerifyForm(state, viewModel)
        }
    }
}

@Composable
private fun Notice(text: String, error: Boolean) {
    Card(Modifier.fillMaxWidth()) {
        Text(
            text,
            modifier = Modifier.padding(12.dp),
            style = MaterialTheme.typography.bodySmall,
            color = if (error) MaterialTheme.colorScheme.error else MaterialTheme.colorScheme.primary,
        )
    }
}

@Composable
private fun SignInForm(state: AuthState, viewModel: AuthViewModel) {
    OutlinedTextField(
        value = state.email,
        onValueChange = viewModel::onEmail,
        label = { Text("Email") },
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
        modifier = Modifier.fillMaxWidth(),
    )
    OutlinedTextField(
        value = state.password,
        onValueChange = viewModel::onPassword,
        label = { Text("Password") },
        singleLine = true,
        visualTransformation = PasswordVisualTransformation(),
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
        modifier = Modifier.fillMaxWidth(),
    )

    PrimaryButton("Sign in", state.busy) { viewModel.signIn() }

    Row(
        Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Text("New here?", style = MaterialTheme.typography.bodySmall)
        TextButton(onClick = { viewModel.showStep(AuthStep.REGISTER) }) { Text("Create an account") }
    }
}

@Composable
private fun RegisterForm(state: AuthState, viewModel: AuthViewModel) {
    OutlinedTextField(
        value = state.name,
        onValueChange = viewModel::onName,
        label = { Text("Full name") },
        singleLine = true,
        modifier = Modifier.fillMaxWidth(),
    )
    OutlinedTextField(
        value = state.email,
        onValueChange = viewModel::onEmail,
        label = { Text("Email") },
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
        modifier = Modifier.fillMaxWidth(),
    )
    OutlinedTextField(
        value = state.contact,
        onValueChange = viewModel::onContact,
        label = { Text("Mobile number (optional)") },
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
        modifier = Modifier.fillMaxWidth(),
    )
    OutlinedTextField(
        value = state.password,
        onValueChange = viewModel::onPassword,
        label = { Text("Password") },
        supportingText = { Text("At least 8 characters") },
        singleLine = true,
        visualTransformation = PasswordVisualTransformation(),
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
        modifier = Modifier.fillMaxWidth(),
    )

    PrimaryButton("Send my code", state.busy) { viewModel.register() }

    Row(
        Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Text("Already have one?", style = MaterialTheme.typography.bodySmall)
        TextButton(onClick = { viewModel.showStep(AuthStep.SIGN_IN) }) { Text("Sign in") }
    }
}

@Composable
private fun VerifyForm(state: AuthState, viewModel: AuthViewModel) {
    Text(
        "We sent a 6 digit code to ${state.email}.",
        style = MaterialTheme.typography.bodySmall,
    )

    OutlinedTextField(
        value = state.code,
        onValueChange = viewModel::onCode,
        label = { Text("6 digit code") },
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.NumberPassword),
        textStyle = MaterialTheme.typography.headlineSmall.copy(textAlign = TextAlign.Center),
        modifier = Modifier.fillMaxWidth(),
    )

    PrimaryButton("Confirm", state.busy) { viewModel.verify() }

    Row(
        Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.CenterVertically,
    ) {
        TextButton(onClick = { viewModel.resend() }, enabled = !state.busy) { Text("Send another code") }
        TextButton(onClick = { viewModel.showStep(AuthStep.SIGN_IN) }) { Text("Back") }
    }
}

@Composable
private fun PrimaryButton(label: String, busy: Boolean, onClick: () -> Unit) {
    Button(onClick = onClick, enabled = !busy, modifier = Modifier.fillMaxWidth()) {
        if (busy) {
            CircularProgressIndicator(
                modifier = Modifier.height(18.dp),
                strokeWidth = 2.dp,
                color = MaterialTheme.colorScheme.onPrimary,
            )
            Spacer(Modifier.height(0.dp))
        } else {
            Text(label)
        }
    }
}

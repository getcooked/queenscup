package ph.queenscup.customer

import android.Manifest
import android.os.Build
import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.AccountCircle
import androidx.compose.material.icons.outlined.ChatBubbleOutline
import androidx.compose.material.icons.outlined.LocalCafe
import androidx.compose.material.icons.outlined.Receipt
import androidx.compose.material.icons.outlined.ShoppingBag
import androidx.compose.material3.Badge
import androidx.compose.material3.BadgedBox
import androidx.compose.material3.Icon
import androidx.compose.material3.NavigationBar
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavDestination.Companion.hierarchy
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import ph.queenscup.customer.ui.BasketViewModel
import ph.queenscup.customer.ui.account.AccountScreen
import ph.queenscup.customer.ui.account.AuthViewModel
import ph.queenscup.customer.ui.chat.ChatScreen
import ph.queenscup.customer.ui.chat.ChatViewModel
import ph.queenscup.customer.ui.cart.BasketScreen
import ph.queenscup.customer.ui.menu.MenuScreen
import ph.queenscup.customer.ui.theme.QueensCupTheme
import ph.queenscup.customer.ui.track.TrackScreen

class MainActivity : ComponentActivity() {

    private val notificationPermission =
        registerForActivityResult(ActivityResultContracts.RequestPermission()) { }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        // From Android 13 the "your order is ready" notification needs consent.
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            notificationPermission.launch(Manifest.permission.POST_NOTIFICATIONS)
        }

        // Set when the customer taps a push notification.
        val deepLinkReference = intent?.getStringExtra(EXTRA_REFERENCE)

        setContent {
            QueensCupTheme {
                QueensCupApp(deepLinkReference)
            }
        }
    }

    companion object {
        const val EXTRA_REFERENCE = "reservation_reference"
    }
}

private enum class Tab(
    val route: String,
    val label: String,
    val icon: ImageVector,
) {
    MENU("menu", "Menu", Icons.Outlined.LocalCafe),
    BASKET("basket", "Reserve", Icons.Outlined.ShoppingBag),
    TRACK("track", "Orders", Icons.Outlined.Receipt),
    CHAT("chat", "Help", Icons.Outlined.ChatBubbleOutline),
    ACCOUNT("account", "Account", Icons.Outlined.AccountCircle),
}

@Composable
private fun QueensCupApp(deepLinkReference: String?) {
    val navController = rememberNavController()
    val basketViewModel: BasketViewModel = viewModel()
    val authViewModel: AuthViewModel = viewModel()
    val chatViewModel: ChatViewModel = viewModel()
    val basket by basketViewModel.state.collectAsStateWithLifecycle()

    // A push tap lands straight on the tracker for that reservation.
    val startRoute = remember { mutableStateOf(if (deepLinkReference != null) Tab.TRACK.route else Tab.MENU.route) }

    val backStackEntry by navController.currentBackStackEntryAsState()
    val currentDestination = backStackEntry?.destination

    Scaffold(
        bottomBar = {
            NavigationBar {
                Tab.entries.forEach { tab ->
                    val selected = currentDestination?.hierarchy?.any { it.route == tab.route } == true

                    NavigationBarItem(
                        selected = selected,
                        onClick = {
                            navController.navigate(tab.route) {
                                // Keep a single copy of each tab and preserve
                                // its scroll position, the way a native app
                                // behaves when you move between tabs.
                                popUpTo(navController.graph.findStartDestination().id) { saveState = true }
                                launchSingleTop = true
                                restoreState = true
                            }
                        },
                        icon = {
                            if (tab == Tab.BASKET && basket.cupCount > 0) {
                                BadgedBox(badge = { Badge { Text(basket.cupCount.toString()) } }) {
                                    Icon(tab.icon, contentDescription = tab.label)
                                }
                            } else {
                                Icon(tab.icon, contentDescription = tab.label)
                            }
                        },
                        label = { Text(tab.label) },
                    )
                }
            }
        }
    ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = startRoute.value,
            modifier = Modifier.padding(innerPadding),
        ) {
            composable(Tab.MENU.route) {
                MenuScreen(
                    viewModel = basketViewModel,
                    onViewBasket = { navController.navigate(Tab.BASKET.route) },
                )
            }
            composable(Tab.BASKET.route) {
                BasketScreen(
                    viewModel = basketViewModel,
                    onBrowseMenu = { navController.navigate(Tab.MENU.route) },
                    onTrackReservation = { navController.navigate(Tab.TRACK.route) },
                )
            }
            composable(Tab.TRACK.route) {
                TrackScreen(initialReference = deepLinkReference)
            }
            composable(Tab.CHAT.route) {
                ChatScreen(viewModel = chatViewModel)
            }
            composable(Tab.ACCOUNT.route) {
                // Shares the activity-scoped view models so the signed-in
                // name and the cup fee match what Reserve is showing.
                AccountScreen(viewModel = basketViewModel, authViewModel = authViewModel)
            }
        }
    }
}

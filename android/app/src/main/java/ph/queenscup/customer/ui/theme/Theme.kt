package ph.queenscup.customer.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Typography
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.sp

// Matches the web admin palette so the two halves of the product feel related.
private val Accent = Color(0xFF12864E)
private val AccentLight = Color(0xFF16A65F)
private val AccentDark = Color(0xFF0C6F3F)
private val Gold = Color(0xFF2F9E62)
private val Danger = Color(0xFFE53170)
private val Surface = Color(0xFFF7FBF8)
private val OnSurface = Color(0xFF123524)
private val Muted = Color(0xFF5F7F6B)

private val LightColors = lightColorScheme(
    primary = Accent,
    onPrimary = Color.White,
    primaryContainer = Color(0xFFD7F0E1),
    onPrimaryContainer = AccentDark,
    secondary = Gold,
    onSecondary = Color.White,
    background = Surface,
    onBackground = OnSurface,
    surface = Color.White,
    onSurface = OnSurface,
    surfaceVariant = Color(0xFFE9F7EE),
    onSurfaceVariant = Muted,
    error = Danger,
    outline = Color(0xFFCFE7D7),
)

private val DarkColors = darkColorScheme(
    primary = AccentLight,
    onPrimary = Color(0xFF04150C),
    primaryContainer = AccentDark,
    onPrimaryContainer = Color.White,
    secondary = Gold,
    background = Color(0xFF0B1710),
    onBackground = Color(0xFFE6F2EA),
    surface = Color(0xFF122019),
    onSurface = Color(0xFFE6F2EA),
    surfaceVariant = Color(0xFF1B2E23),
    onSurfaceVariant = Color(0xFFA8C4B4),
    error = Color(0xFFFF6E9C),
    outline = Color(0xFF2C4436),
)

private val AppTypography = Typography(
    headlineSmall = TextStyle(fontSize = 22.sp, fontWeight = FontWeight.Bold),
    titleMedium = TextStyle(fontSize = 16.sp, fontWeight = FontWeight.SemiBold),
    bodyMedium = TextStyle(fontSize = 14.sp),
    labelSmall = TextStyle(fontSize = 11.sp, fontWeight = FontWeight.Medium),
)

@Composable
fun QueensCupTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    content: @Composable () -> Unit,
) {
    MaterialTheme(
        colorScheme = if (darkTheme) DarkColors else LightColors,
        typography = AppTypography,
        content = content,
    )
}

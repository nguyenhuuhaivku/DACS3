package com.example.restaurant.ui.theme

import android.app.Activity
import android.os.Build
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.dynamicDarkColorScheme
import androidx.compose.material3.dynamicLightColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.runtime.SideEffect
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.toArgb
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalView
import androidx.core.view.WindowCompat

private val DarkColorScheme = darkColorScheme(
    primary = Gold,
    onPrimary = RichBlack,
    primaryContainer = DarkGold,
    onPrimaryContainer = SoftCream,
    secondary = LightGold,
    onSecondary = DeepCharcoal,
    secondaryContainer = DarkGold,
    onSecondaryContainer = SoftCream,
    tertiary = DarkBurgundy,
    onTertiary = CreamWhite,
    background = RichBlack,
    onBackground = CreamWhite,
    surface = DeepCharcoal,
    onSurface = CreamWhite,
    surfaceVariant = SmokeGray,
    onSurfaceVariant = LightGold,
    error = ErrorRed,
    onError = CreamWhite
)

private val LightColorScheme = lightColorScheme(
    primary = DarkGold,
    onPrimary = CreamWhite,
    primaryContainer = LightGold,
    onPrimaryContainer = DeepCharcoal,
    secondary = Gold,
    onSecondary = RichBlack,
    secondaryContainer = LightGold,
    onSecondaryContainer = DeepCharcoal,
    tertiary = DarkBurgundy,
    onTertiary = CreamWhite,
    background = LightSurface,
    onBackground = RichBlack,
    surface = CreamWhite,
    onSurface = DeepCharcoal,
    surfaceVariant = SoftCream,
    onSurfaceVariant = SmokeGray,
    error = ErrorRed,
    onError = CreamWhite
)

@Composable
fun RestaurantTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    // Dynamic color is available on Android 12+
    dynamicColor: Boolean = false, // Disabled dynamic colors to keep our elegant theme consistent
    content: @Composable () -> Unit
) {
    val colorScheme = when {
        dynamicColor && Build.VERSION.SDK_INT >= Build.VERSION_CODES.S -> {
            val context = LocalContext.current
            if (darkTheme) dynamicDarkColorScheme(context) else dynamicLightColorScheme(context)
        }
        darkTheme -> DarkColorScheme
        else -> LightColorScheme
    }
    val view = LocalView.current
    if (!view.isInEditMode) {
        SideEffect {
            val window = (view.context as Activity).window
            
            // Set status bar color to transparent for edge-to-edge experience
            window.statusBarColor = Color.Transparent.toArgb()
            window.navigationBarColor = Color.Transparent.toArgb()
            
            // Adjust status bar icons based on theme
            WindowCompat.getInsetsController(window, view).apply {
                isAppearanceLightStatusBars = !darkTheme
                isAppearanceLightNavigationBars = !darkTheme
            }
        }
    }

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography,
        content = content
    )
} 
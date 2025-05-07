package com.example.restaurant

import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.WindowManager
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.compose.BackHandler
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.layout.windowInsetsPadding
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccountCircle
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Login
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.Menu
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.RestaurantMenu
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material.icons.filled.ShoppingCart
import androidx.compose.material.icons.automirrored.filled.EventNote
import androidx.compose.material.icons.filled.Email
import androidx.compose.material.icons.filled.Image
import androidx.compose.material.icons.filled.History
import androidx.compose.material.icons.filled.Receipt
import androidx.compose.material3.Button
import androidx.compose.material3.Divider
import androidx.compose.material3.DrawerValue
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.ModalDrawerSheet
import androidx.compose.material3.ModalNavigationDrawer
import androidx.compose.material3.NavigationDrawerItem
import androidx.compose.material3.NavigationDrawerItemDefaults
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.rememberDrawerState
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.livedata.observeAsState
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.ColorFilter
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.core.view.WindowCompat
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavDestination.Companion.hierarchy
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import androidx.navigation.NavType
import com.example.restaurant.ui.components.FloatingCartButton
import com.example.restaurant.ui.components.DeliveryDialog
import com.example.restaurant.ui.screens.CartScreen
import com.example.restaurant.ui.screens.ForgotPasswordScreen
import com.example.restaurant.ui.screens.HomeScreen
import com.example.restaurant.ui.screens.LoginScreen
import com.example.restaurant.ui.screens.MenuScreen
import com.example.restaurant.ui.screens.RegisterScreen
import com.example.restaurant.ui.screens.ReservationScreen
import com.example.restaurant.ui.screens.SettingsScreen
import com.example.restaurant.ui.screens.TakeawayScreen
import com.example.restaurant.ui.screens.CurrentOrdersScreen
import com.example.restaurant.ui.screens.OrderHistoryScreen
import com.example.restaurant.ui.screens.ReservationDetailScreen
import com.example.restaurant.ui.theme.DarkBurgundy
import com.example.restaurant.ui.theme.Gold
import com.example.restaurant.ui.theme.RichBlack
import com.example.restaurant.ui.theme.RestaurantTheme
import com.example.restaurant.ui.viewmodel.AuthViewModel
import com.example.restaurant.ui.viewmodel.CartViewModel
import com.example.restaurant.ui.viewmodel.MenuViewModel
import kotlinx.coroutines.launch
import androidx.compose.foundation.layout.safeDrawingPadding
import androidx.compose.foundation.layout.WindowInsets
import androidx.compose.foundation.layout.safeDrawing
import androidx.compose.foundation.layout.only
import androidx.compose.foundation.layout.WindowInsetsSides
import androidx.compose.material.icons.filled.LocalShipping
import com.example.restaurant.ui.screens.TakeawayOrderDetailScreen
import com.example.restaurant.ui.screens.TakeawayOrdersScreen

class MainActivity : ComponentActivity() {
    @OptIn(ExperimentalMaterial3Api::class)
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Chuyển ngay sang giao diện chính nếu đã khôi phục trạng thái
        if (savedInstanceState != null) {
            WindowCompat.setDecorFitsSystemWindows(window, false)
            setContent {
                RestaurantTheme {
                    Surface(
                        modifier = Modifier.fillMaxSize(),
                        color = MaterialTheme.colorScheme.background
                    ) {
                        RestaurantApp()
                    }
                }
            }
            return
        }
        
        // Hiển thị splash screen trong 1.5 giây
        Handler(Looper.getMainLooper()).postDelayed({
            // Sau khi delay, hiển thị giao diện chính
            window.clearFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN)
            WindowCompat.setDecorFitsSystemWindows(window, false)
            setContent {
                RestaurantTheme {
                    Surface(
                        modifier = Modifier.fillMaxSize(),
                        color = MaterialTheme.colorScheme.background
                    ) {
                        RestaurantApp()
                    }
                }
            }
        }, 1500) // Delay 1.5 giây
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun RestaurantApp() {
    val drawerState = rememberDrawerState(initialValue = DrawerValue.Closed)
    val scope = rememberCoroutineScope()
    val navController = rememberNavController()
    val authViewModel: AuthViewModel = viewModel()
    val cartViewModel: CartViewModel = viewModel()
    
    // Sử dụng mutableState cho trạng thái đăng nhập để dễ cập nhật
    var isLoggedIn by remember { mutableStateOf(authViewModel.repository.isLoggedIn()) }
    var currentUser by remember { mutableStateOf(authViewModel.repository.getCurrentUser()) }
    
    // Quan sát sự thay đổi trạng thái từ ViewModel
    val isLoggedInFromVM by authViewModel.isLoggedIn.observeAsState(false)
    val currentUserFromVM by authViewModel.currentUser.observeAsState()
    
    // Cập nhật trạng thái từ ViewModel khi có thay đổi
    LaunchedEffect(isLoggedInFromVM, currentUserFromVM) {
        if (isLoggedInFromVM != isLoggedIn) {
            isLoggedIn = isLoggedInFromVM
        }
        if (currentUserFromVM != null && currentUserFromVM != currentUser) {
            currentUser = currentUserFromVM
        }
    }
    
    // Refresh trạng thái đăng nhập trực tiếp từ repository để đảm bảo cập nhật ngay lập tức
    LaunchedEffect(Unit) {
        // Kiểm tra trạng thái đăng nhập ban đầu
        isLoggedIn = authViewModel.repository.isLoggedIn()
        currentUser = authViewModel.repository.getCurrentUser()
    }
    
    // Cart data
    val cartItems by cartViewModel.cartItems.observeAsState(emptyList())
    val cartTotal by cartViewModel.cartTotal.observeAsState(0.0)
    val cartItemCount by cartViewModel.cartItemCount.observeAsState(0)
    
    // Enhanced LaunchedEffect to respond to auth state changes
    LaunchedEffect(key1 = isLoggedIn) {
        if (isLoggedIn) {
            // When user logs in, we immediately refresh user data
            authViewModel.refreshUserData()
            // Đảm bảo UI được cập nhật với thông tin người dùng mới nhất
            currentUser = authViewModel.repository.getCurrentUser()
            
            // Refresh cart data to ensure it's up to date
            cartViewModel.loadCartItems()
            
            // If we have an anonymous cart, transfer it to the user
            if (cartItemCount > 0) {
                cartViewModel.transferAnonymousCartToUser()
            }
        } else {
            // When user logs out, we need to reset to anonymous cart
            cartViewModel.loadCartItems() // This will load the anonymous cart when no user ID
        }
    }
    
    // Main screens
    val mainScreens = listOf(
        Screen.Home,
        Screen.Menu,
        Screen.Cart,
        Screen.Reservation,
        Screen.CurrentOrders,
        Screen.TakeawayOrders,
        Screen.OrderHistory,
        Screen.Settings
    )
    
    // Current route for determining when to show floating cart button
    val currentBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = currentBackStackEntry?.destination?.route
    val showFloatingCart = (currentRoute == Screen.Home.route || currentRoute == Screen.Menu.route) && cartItemCount > 0
    
    // State for delivery dialog
    var showDeliveryDialog by remember { mutableStateOf(false) }
    
    ModalNavigationDrawer(
        drawerState = drawerState,
        gesturesEnabled = true,
        drawerContent = {
            // Drawer content với thiết kế hiện đại, transparent
            Column(
                modifier = Modifier
                    .fillMaxHeight()
                    .width(280.dp)
                    .background(
                        color = Color(0xE6121212), // Almost transparent rich black
                        shape = RoundedCornerShape(topEnd = 16.dp, bottomEnd = 16.dp)
                    )
                    .shadow(
                        elevation = 20.dp,
                        shape = RoundedCornerShape(topEnd = 16.dp, bottomEnd = 16.dp)
                    )
            ) {
                // User header - Modern transparent design
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 48.dp, bottom = 16.dp, start = 16.dp, end = 16.dp)
                ) {
                    Column {
                        // User avatar và thông tin
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                            val currentLoggedIn = authViewModel.repository.isLoggedIn()
                            val currentUserInfo = authViewModel.repository.getCurrentUser()
                            
                            // Avatar with different icon based on login status with elevation and larger size
                            Box(
                                modifier = Modifier
                                    .size(72.dp)
                                    .clip(CircleShape)
                                    .background(Gold.copy(alpha = 0.9f))
                                    .shadow(8.dp, CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Icon(
                                    imageVector = if (currentLoggedIn) Icons.Default.Person else Icons.Default.AccountCircle,
                                    contentDescription = "Avatar",
                                    modifier = Modifier.size(40.dp),
                                    tint = Color.White
                                )
                            }
                            
                            Spacer(modifier = Modifier.width(16.dp))
                            
                            Column(
                                modifier = Modifier.weight(1f)
                            ) {
                                // Greeting message with username if logged in
                                Text(
                                    text = if (currentLoggedIn) 
                                        stringResource(R.string.greeting, currentUserInfo?.name ?: "") 
                                    else 
                                        stringResource(R.string.guest),
                                    style = MaterialTheme.typography.titleLarge,
                                    fontWeight = FontWeight.Bold,
                                    maxLines = 1,
                                    overflow = TextOverflow.Ellipsis,
                                    color = Color.White
                                )
                                
                                // Email nếu đã đăng nhập
                                if (currentLoggedIn && currentUserInfo?.email != null) {
                                    Text(
                                        text = currentUserInfo.email ?: "",
                                        style = MaterialTheme.typography.bodyMedium,
                                        maxLines = 1,
                                        overflow = TextOverflow.Ellipsis,
                                        color = Color.White.copy(alpha = 0.7f)
                                    )
                                }
                            }
                            
                            // Nút đăng nhập/đăng xuất với phong cách hiện đại
                            if (currentLoggedIn) {
                                IconButton(
                                    onClick = {
                                        scope.launch {
                                            drawerState.close()
                                            // Logout and refresh all states
                                            authViewModel.logout()
                                            // After logout, make sure UI updates immediately
                                            authViewModel.refreshUserData()
                                        }
                                    },
                                    modifier = Modifier
                                        .size(48.dp)
                                        .background(
                                            color = DarkBurgundy.copy(alpha = 0.8f),
                                            shape = CircleShape
                                        )
                                ) {
                                    Icon(
                                        imageVector = Icons.Default.Logout, 
                                        contentDescription = stringResource(R.string.logout),
                                        tint = Color.White,
                                        modifier = Modifier.size(24.dp)
                                    )
                                }
                            } else {
                                IconButton(
                                    onClick = {
                                        scope.launch {
                                            drawerState.close()
                                            navController.navigate(Screen.Login.route) {
                                                launchSingleTop = true
                                            }
                                        }
                                    },
                                    modifier = Modifier
                                        .size(48.dp)
                                        .background(
                                            color = Gold.copy(alpha = 0.8f),
                                            shape = CircleShape
                                        )
                                ) {
                                    Icon(
                                        imageVector = Icons.Default.Login, 
                                        contentDescription = stringResource(R.string.login),
                                        tint = RichBlack,
                                        modifier = Modifier.size(24.dp)
                                    )
                                }
                            }
                        }
                    }
                }
                
                // Subtle divider
                Divider(
                    modifier = Modifier.padding(horizontal = 24.dp),
                    color = Color.White.copy(alpha = 0.2f),
                    thickness = 1.dp
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                // Navigation items
                val navBackStackEntry by navController.currentBackStackEntryAsState()
                val currentDestination = navBackStackEntry?.destination
                
                // Scrollable menu items
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 8.dp)
                ) {
                    mainScreens.forEach { screen ->
                        val isTakeawayItem = screen.route == Screen.Takeaway.route
                        
                        // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                        val currentLoggedIn = authViewModel.repository.isLoggedIn()
                        
                        // If it's the takeaway option, only show if user is logged in
                        if (!isTakeawayItem || (isTakeawayItem && currentLoggedIn)) {
                            val isSelected = currentDestination?.hierarchy?.any { it.route == screen.route } == true
                            
                            // Modern styling for navigation items
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 4.dp, horizontal = 8.dp)
                                    .clip(RoundedCornerShape(12.dp))
                                    .background(
                                        if (isSelected) Gold.copy(alpha = 0.2f)
                                        else Color.Transparent
                                    )
                                    .clickable {
                                        scope.launch { drawerState.close() }
                                        
                                        if (screen.route == Screen.Takeaway.route || 
                                            screen.route == Screen.Reservation.route || 
                                            screen.route == Screen.Cart.route) {
                                            
                                            // Kiểm tra lại trạng thái đăng nhập ngay lúc click
                                            val isCurrentlyLoggedIn = authViewModel.repository.isLoggedIn()
                                            
                                            if (isCurrentlyLoggedIn) {
                                                // Xóa back stack hiện tại và khởi tạo lại từ trang Home
                                                navController.navigate(screen.route) {
                                                    // Xóa toàn bộ back stack và đặt màn hình đích làm màn hình gốc mới
                                                    popUpTo(Screen.Home.route) {
                                                        inclusive = false
                                                    }
                                                    launchSingleTop = true
                                                }
                                            } else {
                                                navController.navigate(Screen.Login.route) {
                                                    launchSingleTop = true
                                                }
                                            }
                                        } else {
                                            // Điều hướng đến các màn hình khác với cách xóa back stack
                                            navController.navigate(screen.route) {
                                                // Xóa toàn bộ back stack và đặt màn hình đích làm màn hình gốc mới
                                                popUpTo(Screen.Home.route) {
                                                    inclusive = false
                                                }
                                                launchSingleTop = true
                                            }
                                        }
                                    }
                                    .padding(12.dp)
                            ) {
                                Row(
                                    verticalAlignment = Alignment.CenterVertically,
                                    modifier = Modifier.fillMaxWidth()
                                ) {
                                    // Icongraphy with elevation
                                    Box(
                                        modifier = Modifier
                                            .size(40.dp)
                                            .clip(CircleShape)
                                            .background(
                                                if (isSelected) Gold 
                                                else Color.White.copy(alpha = 0.1f)
                                            )
                                            .padding(8.dp),
                                        contentAlignment = Alignment.Center
                                    ) {
                                        Icon(
                                            imageVector = screen.icon,
                                            contentDescription = null,
                                            tint = if (isSelected) RichBlack else Color.White,
                                            modifier = Modifier.size(20.dp)
                                        )
                                    }
                                    
                                    Spacer(modifier = Modifier.width(16.dp))
                                    
                                    // Label text
                                    Text(
                                        text = stringResource(screen.resourceId),
                                        style = MaterialTheme.typography.bodyLarge,
                                        fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal,
                                        color = if (isSelected) Gold else Color.White
                                    )
                                }
                            }
                        }
                    }
                }
                
                // App branding at bottom
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f),
                    contentAlignment = Alignment.BottomCenter
                ) {
                    Column(
                        horizontalAlignment = Alignment.CenterHorizontally,
                        modifier = Modifier.padding(bottom = 24.dp)
                    ) {
                        Text(
                            text = stringResource(R.string.app_name),
                            style = MaterialTheme.typography.titleMedium,
                            color = Gold,
                            fontWeight = FontWeight.Bold
                        )
                        Text(
                            text = "© 2024",
                            style = MaterialTheme.typography.bodySmall,
                            color = Color.White.copy(alpha = 0.5f)
                        )
                    }
                }
            }
        },
        content = {
            Scaffold(
                topBar = {
                    // Only show TopAppBar if not on auth screens
                    val currentRoute = navController.currentBackStackEntryAsState().value?.destination?.route
                    val authScreens = listOf(Screen.Login.route, Screen.Register.route, Screen.ForgotPassword.route)
                    
                    if (currentRoute !in authScreens) {
                        // Modern sleek top bar
                        TopAppBar(
                            title = { 
                                Row(
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Icon(
                                        imageVector = Icons.Default.RestaurantMenu,
                                        contentDescription = null,
                                        tint = Gold,
                                        modifier = Modifier.size(28.dp)
                                    )
                                    
                                    Spacer(modifier = Modifier.width(12.dp))
                                    
                                    Text(
                                        text = stringResource(R.string.app_name),
                                        style = MaterialTheme.typography.titleLarge,
                                        fontWeight = FontWeight.Bold,
                                        color = MaterialTheme.colorScheme.onSurface
                                    )
                                }
                            },
                            navigationIcon = {
                                IconButton(
                                    onClick = { scope.launch { drawerState.open() } },
                                    modifier = Modifier
                                        .padding(8.dp)
                                        .size(48.dp)
                                ) {
                                    Icon(
                                        Icons.Default.Menu, 
                                        contentDescription = "Menu",
                                        modifier = Modifier.size(28.dp),
                                        tint = MaterialTheme.colorScheme.onSurface
                                    )
                                }
                            },
                            modifier = Modifier
                                .shadow(elevation = 4.dp)
                        )
                    }
                },
                modifier = Modifier.windowInsetsPadding(
                    WindowInsets.safeDrawing.only(WindowInsetsSides.Horizontal)
                )
            ) { innerPadding ->
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(innerPadding)
                ) {
                    NavHost(
                        navController = navController,
                        startDestination = Screen.Home.route,
                        modifier = Modifier.fillMaxSize()
                    ) {
                        // Màn hình chính
                        composable(Screen.Home.route) {
                            HomeScreen(
                                navigateToMenu = { 
                                    navController.navigate(Screen.Menu.route) {
                                        // Xóa back stack hiện tại và đặt Home làm gốc
                                        popUpTo(Screen.Home.route) {
                                            inclusive = false
                                        }
                                        launchSingleTop = true
                                    }
                                },
                                navigateToReservation = { 
                                    // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                                    val currentLoginStatus = authViewModel.repository.isLoggedIn()
                                    
                                    if (currentLoginStatus) {
                                        navController.navigate(Screen.Reservation.route) {
                                            // Xóa back stack hiện tại và đặt Home làm gốc
                                            popUpTo(Screen.Home.route) {
                                                inclusive = false
                                            }
                                            launchSingleTop = true
                                        }
                                    } else {
                                        navController.navigate(Screen.Login.route) {
                                            // Lưu lại điểm cần quay lại sau khi đăng nhập
                                            popUpTo(Screen.Home.route) { 
                                                saveState = true 
                                            }
                                            launchSingleTop = true
                                        }
                                    }
                                }
                            )
                        }
                        composable(Screen.Menu.route) {
                            val menuViewModel: MenuViewModel = viewModel()
                            MenuScreen(
                                onAddToCart = { menuItem ->
                                    // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                                    val currentLoginStatus = authViewModel.repository.isLoggedIn()
                                    
                                    if (currentLoginStatus) {
                                        cartViewModel.addToCart(menuItem)
                                    } else {
                                        navController.navigate(Screen.Login.route) {
                                            popUpTo(Screen.Menu.route)
                                        }
                                    }
                                },
                                onNavigateBack = {
                                    // Quay lại trang chủ bằng cách xóa toàn bộ back stack và tạo mới trang Home
                                    navController.navigate(Screen.Home.route) {
                                        popUpTo(navController.graph.startDestinationId) {
                                            inclusive = true
                                        }
                                    }
                                }
                            )
                        }
                        composable(Screen.Cart.route) {
                            // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                            val currentLoggedIn = authViewModel.repository.isLoggedIn()
                            
                            if (currentLoggedIn) {
                                CartScreen(
                                    onBrowseMenu = { navController.navigate(Screen.Menu.route) },
                                    onReservation = { navController.navigate(Screen.Reservation.route) },
                                    onTakeaway = { navController.navigate(Screen.Takeaway.route) }
                                )
                            } else {
                                // Show login required message with button to login
                                Box(
                                    modifier = Modifier
                                        .fillMaxSize(),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Column(
                                        horizontalAlignment = Alignment.CenterHorizontally,
                                        modifier = Modifier.padding(16.dp)
                                    ) {
                                        Text(
                                            text = stringResource(R.string.login_required),
                                            style = MaterialTheme.typography.titleLarge
                                        )
                                        Spacer(modifier = Modifier.height(16.dp))
                                        Button(
                                            onClick = {
                                                navController.navigate(Screen.Login.route) {
                                                    popUpTo(Screen.Cart.route)
                                                }
                                            }
                                        ) {
                                            Text(stringResource(R.string.login))
                                        }
                                    }
                                }
                            }
                        }
                        composable(Screen.Takeaway.route) {
                            val currentLoggedIn = authViewModel.repository.isLoggedIn()
                            
                            if (currentLoggedIn) {
                                TakeawayScreen(
                                    onNavigateToOrderDetail = { orderId ->
                                        navController.navigate("takeaway_order_detail/$orderId")
                                    },
                                    onNavigateToHome = {
                                        navController.navigate(Screen.Home.route) {
                                            popUpTo(navController.graph.id) {
                                                inclusive = true
                                            }
                                        }
                                    },
                                    onNavigateBack = {
                                        navController.popBackStack()
                                    }
                                )
                            } else {
                                // Show login required message
                                Box(
                                    modifier = Modifier.fillMaxSize(),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Column(
                                        horizontalAlignment = Alignment.CenterHorizontally,
                                        modifier = Modifier.padding(16.dp)
                                    ) {
                                        Text(
                                            text = stringResource(R.string.login_required),
                                            style = MaterialTheme.typography.titleLarge
                                        )
                                        Spacer(modifier = Modifier.height(16.dp))
                                        Button(
                                            onClick = {
                                                navController.navigate(Screen.Login.route) {
                                                    popUpTo(Screen.Takeaway.route)
                                                }
                                            }
                                        ) {
                                            Text(stringResource(R.string.login))
                                        }
                                    }
                                }
                            }
                        }
                        composable(Screen.TakeawayOrders.route) {
                            val currentLoggedIn = authViewModel.repository.isLoggedIn()
                            
                            if (currentLoggedIn) {
                                TakeawayOrdersScreen(
                                    onNavigateToOrderDetail = { orderId ->
                                        navController.navigate("takeaway_order_detail/$orderId")
                                    },
                                    onNavigateBack = {
                                        navController.popBackStack()
                                    }
                                )
                            } else {
                                // Show login required message
                                Box(
                                    modifier = Modifier.fillMaxSize(),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Column(
                                        horizontalAlignment = Alignment.CenterHorizontally,
                                        modifier = Modifier.padding(16.dp)
                                    ) {
                                        Text(
                                            text = stringResource(R.string.login_required),
                                            style = MaterialTheme.typography.titleLarge
                                        )
                                        Spacer(modifier = Modifier.height(16.dp))
                                        Button(
                                            onClick = {
                                                navController.navigate(Screen.Login.route) {
                                                    popUpTo(Screen.TakeawayOrders.route)
                                                }
                                            }
                                        ) {
                                            Text(stringResource(R.string.login))
                                        }
                                    }
                                }
                            }
                        }
                        composable(Screen.Reservation.route) {
                            // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                            val currentLoggedIn = authViewModel.repository.isLoggedIn()
                            
                            if (currentLoggedIn) {
                                ReservationScreen(
                                    onNavigateToMenu = { navController.navigate(Screen.Menu.route) },
                                    onNavigateBack = {
                                        // Quay lại trang chủ bằng cách xóa toàn bộ back stack và tạo mới trang Home
                                        navController.navigate(Screen.Home.route) {
                                            popUpTo(navController.graph.startDestinationId) {
                                                inclusive = true
                                            }
                                        }
                                    },
                                    onNavigateToHome = {
                                        // Quay lại trang chủ khi đặt bàn thành công
                                        navController.navigate(Screen.Home.route) {
                                            popUpTo(navController.graph.startDestinationId) {
                                                inclusive = true
                                            }
                                        }
                                    }
                                )
                            } else {
                                // Show login required message with button to login
                                Box(
                                    modifier = Modifier
                                        .fillMaxSize(),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Column(
                                        horizontalAlignment = Alignment.CenterHorizontally,
                                        modifier = Modifier.padding(16.dp)
                                    ) {
                                        Text(
                                            text = stringResource(R.string.login_required),
                                            style = MaterialTheme.typography.titleLarge
                                        )
                                        Spacer(modifier = Modifier.height(16.dp))
                                        Button(
                                            onClick = {
                                                navController.navigate(Screen.Login.route) {
                                                    popUpTo(Screen.Reservation.route)
                                                }
                                            }
                                        ) {
                                            Text(stringResource(R.string.login))
                                        }
                                    }
                                }
                            }
                        }
                        composable(Screen.Settings.route) { 
                            SettingsScreen(
                                onNavigateToLogin = {
                                    navController.navigate(Screen.Login.route)
                                }
                            ) 
                        }
                        
                        // Màn hình Auth
                        composable(Screen.Login.route) {
                            LoginScreen(
                                onNavigateToRegister = { navController.navigate(Screen.Register.route) },
                                onNavigateToForgotPassword = { navController.navigate(Screen.ForgotPassword.route) },
                                onLoginSuccess = {
                                    // Ensure auth state refresh for UI updates
                                    authViewModel.refreshUserData()
                                    
                                    // Force reload cart data
                                    cartViewModel.loadCartItems()
                                    
                                    // Navigate to Home screen and clear back stack
                                    navController.navigate(Screen.Home.route) {
                                        // Pop up to the start destination of the graph to
                                        // avoid building up a large stack of destinations
                                        popUpTo(navController.graph.id) {
                                            inclusive = true 
                                        }
                                    }
                                },
                                onBackClick = {
                                    navController.popBackStack()
                                }
                            )
                        }
                        
                        composable(Screen.Register.route) {
                            RegisterScreen(
                                onNavigateToLogin = { navController.navigate(Screen.Login.route) {
                                    popUpTo(Screen.Login.route) { inclusive = true }
                                }},
                                onRegisterSuccess = {
                                    // Ensure auth state refresh for UI updates
                                    authViewModel.refreshUserData()
                                    
                                    // Force reload cart data
                                    cartViewModel.loadCartItems()
                                    
                                    // Clear back stack and go to home
                                    navController.navigate(Screen.Home.route) {
                                        popUpTo(navController.graph.findStartDestination().id) {
                                            inclusive = false
                                        }
                                        launchSingleTop = true
                                    }
                                }
                            )
                        }
                        
                        composable(Screen.ForgotPassword.route) {
                            ForgotPasswordScreen(
                                onNavigateToLogin = { navController.popBackStack() }
                            )
                        }
                        
                        // Order status and history routes
                        composable(Screen.CurrentOrders.route) {
                            CurrentOrdersScreen(
                                onReservationClick = { reservationId ->
                                    navController.navigate("reservation_detail/$reservationId")
                                }
                            )
                        }
                        
                        composable(Screen.OrderHistory.route) {
                            OrderHistoryScreen(
                                onReservationClick = { reservationId ->
                                    navController.navigate("reservation_detail/$reservationId")
                                }
                            )
                        }
                        
                        // Reservation detail route with ID parameter
                        composable(
                            route = "reservation_detail/{reservationId}",
                            arguments = listOf(
                                navArgument("reservationId") { type = NavType.IntType }
                            )
                        ) { backStackEntry ->
                            val reservationId = backStackEntry.arguments?.getInt("reservationId") ?: 0
                            ReservationDetailScreen(
                                reservationId = reservationId,
                                onNavigateBack = { navController.popBackStack() }
                            )
                        }
                        
                        // Takeaway order detail route
                        composable(
                            route = "takeaway_order_detail/{orderId}",
                            arguments = listOf(
                                navArgument("orderId") { type = NavType.IntType }
                            )
                        ) { backStackEntry ->
                            val orderId = backStackEntry.arguments?.getInt("orderId") ?: 0
                            TakeawayOrderDetailScreen(
                                orderId = orderId,
                                onNavigateBack = { navController.popBackStack() }
                            )
                        }
                    }
                    
                    // Floating Cart Button (only on Home and Menu screens) - Enhanced visual
                    if (showFloatingCart) {
                        Box(
                            modifier = Modifier
                                .fillMaxSize()
                                .padding(bottom = 16.dp, end = 16.dp),
                            contentAlignment = Alignment.BottomEnd
                        ) {
                        FloatingCartButton(
                            cartItemCount = cartItemCount,
                            cartItems = cartItems,
                            cartTotal = cartTotal,
                                onUpdateQuantity = { id, quantity ->
                                    cartViewModel.updateItemQuantity(id, quantity)
                            },
                                onRemoveItem = { id ->
                                    cartViewModel.removeItem(id)
                            },
                            onCheckout = {
                                // Kiểm tra trạng thái đăng nhập hiện tại từ repository
                                    val currentLoginStatus = authViewModel.repository.isLoggedIn()
                                
                                    if (currentLoginStatus) {
                                    navController.navigate(Screen.Reservation.route)
                                } else {
                                    navController.navigate(Screen.Login.route)
                                }
                            },
                            onTakeaway = {
                                    // Hiển thị dialog thay vì chuyển đến màn hình mới
                                    showDeliveryDialog = true
                                }
                            )
                        }
                    }
                    
                    // Delivery dialog
                    if (showDeliveryDialog) {
                        DeliveryDialog(
                            cartItems = cartItems,
                            cartTotal = cartTotal,
                            onDismiss = { showDeliveryDialog = false },
                            onOrderPlaced = {
                                showDeliveryDialog = false
                                cartViewModel.clearCart()
                            }
                        )
                    }
                }
            }
        }
    )
}

sealed class Screen(val route: String, val resourceId: Int, val icon: ImageVector) {
    // Main screens
    object Home : Screen("home", R.string.menu_home, Icons.Default.Home)
    object Menu : Screen("menu", R.string.menu_menu, Icons.Default.RestaurantMenu)
    object Cart : Screen("cart", R.string.menu_cart, Icons.Default.ShoppingCart)
    object Reservation : Screen("reservation", R.string.menu_reservation, Icons.AutoMirrored.Filled.EventNote)
    object Settings : Screen("settings", R.string.menu_settings, Icons.Default.Settings)
    object Takeaway : Screen("takeaway", R.string.menu_takeaway, Icons.Default.LocalShipping)
    
    // Order screens
    object CurrentOrders : Screen("current_orders", R.string.menu_current_orders, Icons.Default.Receipt)
    object OrderHistory : Screen("order_history", R.string.menu_order_history, Icons.Default.History)
    object TakeawayOrders : Screen("takeaway_orders", R.string.menu_takeaway_orders, Icons.Default.LocalShipping)
    
    // Auth screens
    object Login : Screen("login", R.string.login, Icons.Default.Login)
    object Register : Screen("register", R.string.register, Icons.Default.Person)
    object ForgotPassword : Screen("forgot_password", R.string.forgot_password, Icons.Default.Email)
}
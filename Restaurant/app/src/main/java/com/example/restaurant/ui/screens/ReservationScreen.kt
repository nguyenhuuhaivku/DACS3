package com.example.restaurant.ui.screens

import androidx.activity.compose.BackHandler
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccessTime
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.ChevronRight
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Remove
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.DatePicker
import androidx.compose.material3.DatePickerDialog
import androidx.compose.material3.Divider
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TimeInput
import androidx.compose.material3.rememberDatePickerState
import androidx.compose.material3.rememberTimePickerState
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
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.ui.window.DialogProperties
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.restaurant.R
import com.example.restaurant.ui.viewmodel.CartItem
import com.example.restaurant.ui.viewmodel.CartViewModel
import com.example.restaurant.ui.viewmodel.ReservationViewModel
import coil.compose.AsyncImage
import coil.request.ImageRequest
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Date
import java.util.Locale
import androidx.compose.animation.core.*
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.text.style.TextDecoration

data class LocationOption(
    val id: Int,
    val name: String,
    val description: String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReservationScreen(
    cartViewModel: CartViewModel = viewModel(),
    reservationViewModel: ReservationViewModel = viewModel(),
    onNavigateToMenu: () -> Unit = {},
    onNavigateBack: () -> Unit = {},
    onNavigateToHome: () -> Unit = {}
) {
    // Handle back button press
    BackHandler {
        onNavigateBack()
    }
    
    val snackbarHostState = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    val scrollState = rememberScrollState()
    val context = LocalContext.current

    // Lấy dữ liệu giỏ hàng
    val cartItems by cartViewModel.cartItems.observeAsState(emptyList())
    val cartTotal by cartViewModel.cartTotal.observeAsState(0.0)
    
    // Lấy trạng thái đặt bàn
    val isLoading by reservationViewModel.isLoading.observeAsState(false)
    val error by reservationViewModel.error.observeAsState(null)
    val success by reservationViewModel.success.observeAsState(null)
    
    // Theo dõi trạng thái hiển thị dialog thành công
    var showSuccessDialog by remember { mutableStateOf(false) }
    var reservationCode by remember { mutableStateOf("") }
    var countdownSeconds by remember { mutableStateOf(10) }
    
    // Show error messages
    LaunchedEffect(error) {
        error?.let {
            snackbarHostState.showSnackbar(it)
            reservationViewModel.clearMessages()
        }
    }
    
    // Show success dialog and handle reservation success
    LaunchedEffect(success) {
        success?.let { successMessage ->
            // Extract reservation code from success message
            val code = successMessage.substringAfterLast(": ", "")
            reservationCode = code
            
            // Clear cart immediately after successful reservation
            cartViewModel.clearCart()
            
            // Show success dialog instead of navigating back
            showSuccessDialog = true
            
            // Clear the success message
            reservationViewModel.clearMessages()
        }
    }
    
    // Countdown timer for auto-navigation
    LaunchedEffect(showSuccessDialog) {
        if (showSuccessDialog) {
            countdownSeconds = 10
            while (countdownSeconds > 0) {
                delay(1000)
                countdownSeconds--
            }
            // Auto navigate to home after countdown
            if (showSuccessDialog) {
                showSuccessDialog = false
                onNavigateToHome()
            }
        }
    }

    // Tải giỏ hàng khi màn hình hiển thị
    LaunchedEffect(key1 = Unit) {
        cartViewModel.loadCartItems()
    }

    // Reservation state
    var name by remember { mutableStateOf("") }
    var phoneNumber by remember { mutableStateOf("") }
    var guestCount by remember { mutableStateOf(2) }
    var specialRequests by remember { mutableStateOf("") }
    var selectedLocation by remember { mutableStateOf<LocationOption?>(null) }
    
    // Date and time state
    val datePickerState = rememberDatePickerState(
        initialSelectedDateMillis = System.currentTimeMillis() + (24 * 60 * 60 * 1000) // tomorrow
    )
    val timePickerState = rememberTimePickerState(
        initialHour = 19, // 7 PM
        initialMinute = 0,
        is24Hour = true
    )
    var showDatePicker by remember { mutableStateOf(false) }
    var showTimePicker by remember { mutableStateOf(false) }
    
    // Format selected date and time
    val selectedDate = remember(datePickerState.selectedDateMillis) {
        datePickerState.selectedDateMillis?.let {
            SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(it))
        } ?: ""
    }
    
    val selectedTime = remember(timePickerState.hour, timePickerState.minute) {
        String.format("%02d:%02d", timePickerState.hour, timePickerState.minute)
    }
    
    // Form validation
    var nameError by remember { mutableStateOf<String?>(null) }
    var phoneError by remember { mutableStateOf<String?>(null) }
    
    // Các tùy chọn vị trí
    val locationOptions = remember {
        listOf(
            LocationOption(1, "Trong nhà", "Khu vực có điều hòa, yên tĩnh"),
            LocationOption(2, "Ngoài sân", "Không gian thoáng đãng, view đẹp"),
            LocationOption(3, "VIP", "Phòng riêng, dịch vụ cao cấp")
        )
    }

    // Success Dialog
    if (showSuccessDialog) {
        SuccessReservationDialog(
            reservationCode = reservationCode,
            countdownSeconds = countdownSeconds,
            onDismiss = { 
                showSuccessDialog = false
                onNavigateToHome()
            }
        )
    }

    Scaffold(
        snackbarHost = { SnackbarHost(snackbarHostState) }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp)
                .verticalScroll(scrollState),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Reservation header
            Text(
                text = stringResource(R.string.reservation_title),
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold
            )
            
            Text(
                text = stringResource(R.string.reservation_subtitle),
                style = MaterialTheme.typography.bodyLarge,
                color = MaterialTheme.colorScheme.onSurfaceVariant
            )
            
            Spacer(modifier = Modifier.height(8.dp))
            
            // Personal information
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    Text(
                        text = stringResource(R.string.personal_info),
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    // Name field
                    OutlinedTextField(
                        value = name,
                        onValueChange = { 
                            name = it
                            nameError = null
                        },
                        label = { Text(stringResource(R.string.name)) },
                        leadingIcon = { Icon(Icons.Default.Person, contentDescription = null) },
                        modifier = Modifier.fillMaxWidth(),
                        isError = nameError != null,
                        supportingText = { nameError?.let { Text(it) } },
                        singleLine = true
                    )
                    
                    // Phone field
                    OutlinedTextField(
                        value = phoneNumber,
                        onValueChange = { 
                            phoneNumber = it
                            phoneError = null
                        },
                        label = { Text(stringResource(R.string.phone)) },
                        modifier = Modifier.fillMaxWidth(),
                        isError = phoneError != null,
                        supportingText = { phoneError?.let { Text(it) } },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone)
                    )
                }
            }
            
            // Date and time selection
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    Text(
                        text = stringResource(R.string.date_and_time),
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    // Date selector
                    OutlinedTextField(
                        value = selectedDate,
                        onValueChange = { },
                        readOnly = true,
                        label = { Text(stringResource(R.string.date)) },
                        leadingIcon = { Icon(Icons.Default.CalendarMonth, contentDescription = null) },
                        modifier = Modifier
                            .fillMaxWidth()
                            .clickable { showDatePicker = true },
                        trailingIcon = {
                            IconButton(onClick = { showDatePicker = true }) {
                                Icon(Icons.Default.CalendarMonth, contentDescription = "Select date")
                            }
                        }
                    )
                    
                    // Time selector
                    OutlinedTextField(
                        value = selectedTime,
                        onValueChange = { },
                        readOnly = true,
                        label = { Text(stringResource(R.string.time)) },
                        leadingIcon = { Icon(Icons.Default.AccessTime, contentDescription = null) },
                        modifier = Modifier
                            .fillMaxWidth()
                            .clickable { showTimePicker = true },
                        trailingIcon = {
                            IconButton(onClick = { showTimePicker = true }) {
                                Icon(Icons.Default.AccessTime, contentDescription = "Select time")
                            }
                        }
                    )
                }
            }
            
            // Guest count and location preferences
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    Text(
                        text = stringResource(R.string.reservation_details),
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    // Guest count
                    GuestCountSelector(
                        guestCount = guestCount,
                        onGuestCountChanged = { guestCount = it }
                    )
                    
                    // Location preference
                    Text(
                        text = "Vị trí ưu tiên",
                        style = MaterialTheme.typography.bodyLarge,
                        modifier = Modifier.padding(top = 8.dp)
                    )
                    
                    // Location options
                    Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        locationOptions.forEach { location ->
                            LocationOptionItem(
                                location = location,
                                isSelected = selectedLocation?.id == location.id,
                                onClick = { selectedLocation = location }
                            )
                        }
                    }
                    
                    // Special requests
                    OutlinedTextField(
                        value = specialRequests,
                        onValueChange = { specialRequests = it },
                        label = { Text(stringResource(R.string.special_requests)) },
                        modifier = Modifier.fillMaxWidth(),
                        minLines = 3,
                        maxLines = 5
                    )
                }
            }
            
            // Giỏ hàng món ăn đã chọn
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Header với nút chọn thêm món
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(
                            text = "Món ăn đã chọn",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold
                        )
                        
                        TextButton(
                            onClick = onNavigateToMenu,
                            modifier = Modifier.padding(end = 8.dp)
                        ) {
                            Text("Thêm món")
                            Icon(
                                imageVector = Icons.Default.ChevronRight,
                                contentDescription = null,
                                modifier = Modifier.size(16.dp)
                            )
                        }
                    }
                    
                    if (cartItems.isEmpty()) {
                        // Hiển thị khi chưa có món ăn
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 24.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Column(
                                horizontalAlignment = Alignment.CenterHorizontally,
                                verticalArrangement = Arrangement.Center
                            ) {
                                Icon(
                                    imageVector = Icons.Default.Restaurant,
                                    contentDescription = null,
                                    modifier = Modifier.size(48.dp),
                                    tint = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.5f)
                                )
                                
                                Spacer(modifier = Modifier.height(8.dp))
                                
                                Text(
                                    text = "Chưa có món ăn nào được chọn",
                                    style = MaterialTheme.typography.bodyMedium,
                                    color = MaterialTheme.colorScheme.onSurfaceVariant
                                )
                                
                                Spacer(modifier = Modifier.height(16.dp))
                                
                                Button(
                                    onClick = onNavigateToMenu,
                                    shape = RoundedCornerShape(8.dp)
                                ) {
                                    Text("Chọn món ngay")
                                }
                            }
                        }
                    } else {
                        // Danh sách món ăn đã chọn
                        Column {
                            cartItems.forEach { item ->
                                ReservationCartItem(
                                    item = item,
                                    onQuantityChanged = { newQuantity ->
                                        cartViewModel.updateItemQuantity(item.id, newQuantity)
                                    },
                                    onRemoveItem = {
                                        cartViewModel.removeItem(item.id)
                                    }
                                )
                                Divider(modifier = Modifier.padding(vertical = 8.dp))
                            }
                            
                            // Tổng cộng
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 8.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text(
                                    text = "Tổng cộng:",
                                    style = MaterialTheme.typography.bodyLarge,
                                    fontWeight = FontWeight.Bold
                                )
                                
                                Text(
                                    text = String.format("%,.0f₫", cartTotal),
                                    style = MaterialTheme.typography.bodyLarge,
                                    fontWeight = FontWeight.Bold,
                                    color = MaterialTheme.colorScheme.primary
                                )
                            }
                        }
                    }
                }
            }
            
            // Submit button with loading state
            Button(
                onClick = {
                    // Validate
                    var isValid = true
                    
                    if (name.isBlank()) {
                        nameError = "Vui lòng nhập họ tên"
                        isValid = false
                    }
                    
                    if (phoneNumber.isBlank()) {
                        phoneError = "Vui lòng nhập số điện thoại"
                        isValid = false
                    }
                    
                    // Validate that some items are selected
                    if (cartItems.isEmpty()) {
                        scope.launch {
                            snackbarHostState.showSnackbar("Vui lòng chọn ít nhất một món ăn")
                        }
                        isValid = false
                    }
                    
                    if (isValid) {
                        // Construct reservation date from date and time pickers
                        val calendar = Calendar.getInstance()
                        datePickerState.selectedDateMillis?.let { 
                            calendar.timeInMillis = it 
                        }
                        calendar.set(Calendar.HOUR_OF_DAY, timePickerState.hour)
                        calendar.set(Calendar.MINUTE, timePickerState.minute)
                        calendar.set(Calendar.SECOND, 0)
                        
                        // Submit reservation
                        reservationViewModel.createReservation(
                            name = name,
                            phone = phoneNumber,
                            guestCount = guestCount,
                            reservationDate = calendar.time,
                            note = if (specialRequests.isBlank()) null else specialRequests,
                            cartItems = cartItems,
                            cartViewModel = cartViewModel
                        )
                    }
                },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(56.dp),
                shape = RoundedCornerShape(16.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = MaterialTheme.colorScheme.primary
                ),
                elevation = ButtonDefaults.buttonElevation(
                    defaultElevation = 4.dp
                ),
                enabled = !isLoading
            ) {
                if (isLoading) {
                    CircularProgressIndicator(
                        color = MaterialTheme.colorScheme.onPrimary,
                        modifier = Modifier.size(24.dp),
                        strokeWidth = 2.dp
                    )
                } else {
                Text(
                    text = stringResource(R.string.reserve_table),
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold
                )
                }
            }
            
            Spacer(modifier = Modifier.height(16.dp))
        }
        
        // Date Picker Dialog
        if (showDatePicker) {
            DatePickerDialog(
                onDismissRequest = { showDatePicker = false },
                confirmButton = {
                    TextButton(onClick = { showDatePicker = false }) {
                        Text(stringResource(R.string.confirm))
                    }
                },
                dismissButton = {
                    TextButton(onClick = { showDatePicker = false }) {
                        Text(stringResource(R.string.cancel))
                    }
                }
            ) {
                DatePicker(state = datePickerState)
            }
        }
        
        // Time Picker Dialog
        if (showTimePicker) {
            DatePickerDialog(
                onDismissRequest = { showTimePicker = false },
                confirmButton = {
                    TextButton(onClick = { showTimePicker = false }) {
                        Text(stringResource(R.string.confirm))
                    }
                },
                dismissButton = {
                    TextButton(onClick = { showTimePicker = false }) {
                        Text(stringResource(R.string.cancel))
                    }
                }
            ) {
                TimeInput(state = timePickerState)
            }
        }
    }
}

@Composable
fun LocationOptionItem(
    location: LocationOption,
    isSelected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Row(
        modifier = modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(12.dp))
            .background(
                color = if (isSelected) 
                    MaterialTheme.colorScheme.primaryContainer.copy(alpha = 0.2f)
                else 
                    MaterialTheme.colorScheme.surface
            )
            .border(
                width = 1.dp,
                color = if (isSelected) 
                    MaterialTheme.colorScheme.primary 
                else 
                    MaterialTheme.colorScheme.outlineVariant.copy(alpha = 0.5f),
                shape = RoundedCornerShape(12.dp)
            )
            .clickable(onClick = onClick)
            .padding(vertical = 16.dp, horizontal = 12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        // Radio button
        Box(
            modifier = Modifier
                .size(24.dp)
                .clip(CircleShape)
                .border(
                    width = 2.dp,
                    color = if (isSelected) 
                        MaterialTheme.colorScheme.primary 
                    else 
                        MaterialTheme.colorScheme.outline.copy(alpha = 0.5f),
                    shape = CircleShape
                ),
            contentAlignment = Alignment.Center
        ) {
            if (isSelected) {
                Box(
                    modifier = Modifier
                        .size(12.dp)
                        .clip(CircleShape)
                        .background(MaterialTheme.colorScheme.primary)
                )
            }
        }
        
        Spacer(modifier = Modifier.width(12.dp))
        
        // Location info
        Column {
            Text(
                text = location.name,
                style = MaterialTheme.typography.bodyLarge,
                fontWeight = FontWeight.SemiBold
            )
            
            Text(
                text = location.description,
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant
            )
        }
    }
}

@Composable
fun GuestCountSelector(
    guestCount: Int,
    onGuestCountChanged: (Int) -> Unit,
    modifier: Modifier = Modifier
) {
    Row(
        modifier = modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        Text(
            text = stringResource(R.string.guest_count),
            style = MaterialTheme.typography.bodyLarge
        )
        
        Row(
            verticalAlignment = Alignment.CenterVertically,
            modifier = Modifier
                .clip(RoundedCornerShape(100.dp))
                .background(MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.3f))
                .padding(horizontal = 8.dp, vertical = 4.dp)
        ) {
            // Decrease button
            Box(
                modifier = Modifier
                    .size(32.dp)
                    .clip(CircleShape)
                    .background(
                        color = if (guestCount > 1) 
                            MaterialTheme.colorScheme.primary 
                        else 
                            MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.6f)
                    )
                    .clickable(enabled = guestCount > 1) { 
                        if (guestCount > 1) onGuestCountChanged(guestCount - 1) 
                    },
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.Remove,
                    contentDescription = "Giảm số lượng",
                    modifier = Modifier.size(16.dp),
                    tint = if (guestCount > 1) 
                        MaterialTheme.colorScheme.onPrimary 
                    else 
                        MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.4f)
                )
            }
            
            // Guest count
            Text(
                text = guestCount.toString(),
                style = MaterialTheme.typography.bodyLarge,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(horizontal = 16.dp)
            )
            
            // Increase button
            Box(
                modifier = Modifier
                    .size(32.dp)
                    .clip(CircleShape)
                    .background(MaterialTheme.colorScheme.primary)
                    .clickable { onGuestCountChanged(guestCount + 1) },
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.Add,
                    contentDescription = "Tăng số lượng",
                    modifier = Modifier.size(16.dp),
                    tint = MaterialTheme.colorScheme.onPrimary
                )
            }
        }
    }
}

@Composable
fun ReservationCartItem(
    item: CartItem,
    onQuantityChanged: (Int) -> Unit,
    onRemoveItem: () -> Unit
) {
    val context = LocalContext.current
    
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 4.dp),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface.copy(alpha = 0.8f)
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(8.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Product image
            Box(
                modifier = Modifier
                    .size(56.dp)
                    .clip(RoundedCornerShape(10.dp))
                    .background(MaterialTheme.colorScheme.surfaceVariant)
            ) {
                AsyncImage(
                    model = ImageRequest.Builder(context)
                        .data(item.imageUrl)
                        .crossfade(true)
                        .placeholder(R.drawable.placeholder_food)
                        .error(R.drawable.placeholder_food)
                        .build(),
                    contentDescription = item.name,
                    contentScale = ContentScale.Crop,
                    modifier = Modifier.fillMaxWidth()
                )
            }
            
            Spacer(modifier = Modifier.width(12.dp))
            
            // Product details with price and quantity controls
            Column(
                modifier = Modifier.weight(1f)
            ) {
                // Item name and price
                Text(
                    text = item.name,
                    style = MaterialTheme.typography.bodyLarge,
                    fontWeight = FontWeight.SemiBold,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )
                
                Spacer(modifier = Modifier.height(2.dp))
                
                // Price and quantity controls in one row
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Price with currency formatting
                    Text(
                        text = String.format("%,.0f₫", item.price),
                        style = MaterialTheme.typography.bodyMedium,
                        color = MaterialTheme.colorScheme.primary,
                        fontWeight = FontWeight.Medium
                    )
                    
                    // Quantity controls
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier
                            .clip(RoundedCornerShape(16.dp))
                            .background(MaterialTheme.colorScheme.primaryContainer.copy(alpha = 0.2f))
                            .padding(horizontal = 4.dp, vertical = 2.dp)
                    ) {
                        // Decrease
                        Box(
                            modifier = Modifier
                                .size(24.dp)
                                .clip(CircleShape)
                                .background(
                                    color = if (item.quantity > 1) 
                                        MaterialTheme.colorScheme.primary 
                                    else 
                                        MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.6f)
                                )
                                .clickable(enabled = item.quantity > 1) { 
                                    if (item.quantity > 1) onQuantityChanged(item.quantity - 1) 
                                },
                            contentAlignment = Alignment.Center
                        ) {
                            Icon(
                                imageVector = Icons.Default.Remove,
                                contentDescription = "Giảm số lượng",
                                modifier = Modifier.size(14.dp),
                                tint = if (item.quantity > 1) 
                                    MaterialTheme.colorScheme.onPrimary 
                                else 
                                    MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.4f)
                            )
                        }
                        
                        // Quantity
                        Text(
                            text = item.quantity.toString(),
                            style = MaterialTheme.typography.bodyMedium,
                            fontWeight = FontWeight.Bold,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.padding(horizontal = 8.dp)
                        )
                        
                        // Increase
                        Box(
                            modifier = Modifier
                                .size(24.dp)
                                .clip(CircleShape)
                                .background(MaterialTheme.colorScheme.primary)
                                .clickable { onQuantityChanged(item.quantity + 1) },
                            contentAlignment = Alignment.Center
                        ) {
                            Icon(
                                imageVector = Icons.Default.Add,
                                contentDescription = "Tăng số lượng",
                                modifier = Modifier.size(14.dp),
                                tint = MaterialTheme.colorScheme.onPrimary
                            )
                        }
                    }
                }
            }
            
            Spacer(modifier = Modifier.width(4.dp))
            
            // Remove item button
            Box(
                modifier = Modifier
                    .size(28.dp)
                    .clip(CircleShape)
                    .background(MaterialTheme.colorScheme.errorContainer.copy(alpha = 0.2f))
                    .clickable { onRemoveItem() },
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.Delete,
                    contentDescription = "Xóa",
                    tint = MaterialTheme.colorScheme.error,
                    modifier = Modifier.size(14.dp)
                )
            }
        }
    }
}

@Composable
fun SuccessReservationDialog(
    reservationCode: String,
    countdownSeconds: Int,
    onDismiss: () -> Unit
) {
    // Animation values
    val infiniteTransition = rememberInfiniteTransition(label = "success_animation")
    
    // Scale animation for check icon
    val scale by infiniteTransition.animateFloat(
        initialValue = 1f,
        targetValue = 1.2f,
        animationSpec = infiniteRepeatable(
            animation = tween(800, easing = FastOutSlowInEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "scale"
    )
    
    // Rotate animation for background
    val rotate by infiniteTransition.animateFloat(
        initialValue = 0f,
        targetValue = 360f,
        animationSpec = infiniteRepeatable(
            animation = tween(10000, easing = LinearEasing),
            repeatMode = RepeatMode.Restart
        ),
        label = "rotate"
    )
    
    Dialog(
        onDismissRequest = onDismiss,
        properties = DialogProperties(
            dismissOnBackPress = true,
            dismissOnClickOutside = false
        )
    ) {
        Card(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            shape = RoundedCornerShape(16.dp),
            elevation = CardDefaults.cardElevation(defaultElevation = 8.dp),
            colors = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.surface
            )
        ) {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(24.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                // Success icon with animation
                Box(
                    modifier = Modifier
                        .size(100.dp)
                        .graphicsLayer {
                            rotationZ = rotate
                        }
                        .background(
                            color = MaterialTheme.colorScheme.primaryContainer.copy(alpha = 0.3f),
                            shape = CircleShape
                        ),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.CheckCircle,
                        contentDescription = "Success",
                        modifier = Modifier
                            .size(80.dp)
                            .graphicsLayer {
                                scaleX = scale
                                scaleY = scale
                            },
                        tint = MaterialTheme.colorScheme.primary
                    )
                }
                
                // Success message
                Text(
                    text = "Đặt bàn thành công!",
                    style = MaterialTheme.typography.headlineSmall,
                    fontWeight = FontWeight.Bold,
                    color = MaterialTheme.colorScheme.primary
                )
                
                // Reservation code
                if (reservationCode.isNotEmpty()) {
                    Text(
                        text = "Mã đặt bàn:",
                        style = MaterialTheme.typography.bodyLarge,
                        color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.7f)
                    )
                    
                    Text(
                        text = reservationCode,
                        style = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color = MaterialTheme.colorScheme.primary,
                        textDecoration = TextDecoration.Underline
                    )
                }
                
                Divider(
                    modifier = Modifier.padding(vertical = 8.dp),
                    thickness = 1.dp,
                    color = MaterialTheme.colorScheme.outlineVariant
                )
                
                // Auto-redirect notice
                Text(
                    text = "Tự động chuyển về trang chủ sau $countdownSeconds giây",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
                
                // Home button
                Button(
                    onClick = onDismiss,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = MaterialTheme.colorScheme.primary
                    )
                ) {
                    Icon(
                        imageVector = Icons.Default.Home,
                        contentDescription = null,
                        modifier = Modifier.size(18.dp)
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(
                        text = "Về trang chủ ngay",
                        style = MaterialTheme.typography.labelLarge
                    )
                }
            }
        }
    }
} 
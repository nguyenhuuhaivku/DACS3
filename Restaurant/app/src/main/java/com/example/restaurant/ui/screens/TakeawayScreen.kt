package com.example.restaurant.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.LocationOn
import androidx.compose.material.icons.filled.Phone
import androidx.compose.material.icons.filled.ShoppingCart
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.livedata.observeAsState
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.foundation.clickable
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.restaurant.R
import com.example.restaurant.data.model.TakeawayPaymentMethod
import com.example.restaurant.ui.components.CartItem
import com.example.restaurant.ui.components.LoadingIndicator
import com.example.restaurant.ui.viewmodel.CartViewModel
import com.example.restaurant.ui.viewmodel.TakeawayViewModel
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.Locale

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TakeawayScreen(
    onNavigateToOrderDetail: (Int) -> Unit = {},
    onNavigateToHome: () -> Unit = {},
    onNavigateBack: () -> Unit = {}
) {
    val cartViewModel: CartViewModel = viewModel()
    val takeawayViewModel: TakeawayViewModel = viewModel()
    
    val cartItems by cartViewModel.cartItems.observeAsState(emptyList())
    val cartTotal by cartViewModel.cartTotal.observeAsState(0.0)
    
    val customerName by takeawayViewModel.customerName.observeAsState("")
    val phone by takeawayViewModel.phone.observeAsState("")
    val address by takeawayViewModel.address.observeAsState("")
    val note by takeawayViewModel.note.observeAsState("")
    val paymentMethod by takeawayViewModel.paymentMethod.observeAsState(TakeawayPaymentMethod.CASH_ON_DELIVERY)
    
    val isLoading by takeawayViewModel.isLoading.observeAsState(false)
    val error by takeawayViewModel.error.observeAsState(null)
    val orderSuccess by takeawayViewModel.orderSuccess.observeAsState(false)
    val lastOrderId by takeawayViewModel.lastOrderId.observeAsState(null)
    
    val scope = rememberCoroutineScope()
    val snackbarHostState = remember { SnackbarHostState() }
    val numberFormat = NumberFormat.getCurrencyInstance(Locale("vi", "VN"))
    
    // Handle error messages
    LaunchedEffect(error) {
        error?.let {
            scope.launch {
                snackbarHostState.showSnackbar(
                    message = it,
                    duration = SnackbarDuration.Short
                )
                takeawayViewModel.clearError()
            }
        }
    }
    
    // Navigate to order detail after successful order
    LaunchedEffect(orderSuccess, lastOrderId) {
        if (orderSuccess && lastOrderId != null) {
            // Wait a moment for success animation
            scope.launch {
                snackbarHostState.showSnackbar(
                    message = "Order placed successfully!",
                    duration = SnackbarDuration.Short
                )
                // Reset state after navigating
                takeawayViewModel.resetAfterOrder()
                // Navigate to order detail
                onNavigateToOrderDetail(lastOrderId!!)
            }
        }
    }
    
    Scaffold(
        snackbarHost = { SnackbarHost(snackbarHostState) },
        topBar = {
            TopAppBar(
                title = { Text(text = stringResource(R.string.takeaway_title)) },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        }
    ) { paddingValues ->
        if (orderSuccess) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(paddingValues),
                contentAlignment = Alignment.Center
            ) {
                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    modifier = Modifier.padding(16.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.CheckCircle,
                        contentDescription = null,
                        tint = MaterialTheme.colorScheme.primary,
                        modifier = Modifier.size(80.dp)
                    )
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    Text(
                        text = stringResource(R.string.takeaway_success),
                        style = MaterialTheme.typography.headlineSmall,
                        textAlign = TextAlign.Center
                    )
                    
                    Spacer(modifier = Modifier.height(32.dp))
                    
                    Button(
                        onClick = {
                            takeawayViewModel.resetAfterOrder()
                            onNavigateToHome()
                        }
                    ) {
                        Text(text = stringResource(R.string.continue_shopping))
                    }
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    if (lastOrderId != null) {
                        OutlinedButton(
                            onClick = { onNavigateToOrderDetail(lastOrderId!!) }
                        ) {
                            Text(text = "View Order Details")
                        }
                    }
                }
            }
        } else {
            LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(paddingValues)
                    .padding(horizontal = 16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                item {
                    Text(
                        text = stringResource(R.string.takeaway_title),
                        style = MaterialTheme.typography.headlineMedium,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(vertical = 16.dp)
                    )
                }
                
                if (cartItems.isEmpty()) {
                    item {
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(200.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Column(
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Icon(
                                    imageVector = Icons.Default.ShoppingCart,
                                    contentDescription = null,
                                    modifier = Modifier.size(64.dp),
                                    tint = MaterialTheme.colorScheme.primary.copy(alpha = 0.5f)
                                )
                                
                                Spacer(modifier = Modifier.height(16.dp))
                                
                                Text(
                                    text = stringResource(R.string.empty_cart),
                                    style = MaterialTheme.typography.titleMedium
                                )
                            }
                        }
                    }
                } else {
                    item {
                        Text(
                            text = stringResource(R.string.order_details),
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.SemiBold,
                            modifier = Modifier.padding(top = 8.dp)
                        )
                    }
                    
                    items(cartItems) { cartItem ->
                        CartItem(
                            item = cartItem,
                            onUpdateQuantity = { itemId, quantity ->
                                cartViewModel.updateItemQuantity(itemId, quantity)
                            },
                            onRemoveItem = { itemId ->
                                cartViewModel.removeItem(itemId)
                            }
                        )
                        
                        Divider()
                    }
                    
                    item {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 16.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = stringResource(R.string.total),
                                style = MaterialTheme.typography.titleLarge
                            )
                            
                            Text(
                                text = numberFormat.format(cartTotal),
                                style = MaterialTheme.typography.titleLarge,
                                color = MaterialTheme.colorScheme.primary
                            )
                        }
                    }
                    
                    item {
                        Text(
                            text = stringResource(R.string.delivery_info),
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.SemiBold,
                            modifier = Modifier.padding(top = 16.dp, bottom = 8.dp)
                        )
                    }
                    
                    item {
                        Column(
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            OutlinedTextField(
                                value = customerName,
                                onValueChange = { takeawayViewModel.setCustomerName(it) },
                                label = { Text(stringResource(R.string.name)) },
                                modifier = Modifier.fillMaxWidth(),
                                singleLine = true
                            )
                            
                            Spacer(modifier = Modifier.height(8.dp))
                            
                            OutlinedTextField(
                                value = phone,
                                onValueChange = { takeawayViewModel.setPhone(it) },
                                label = { Text(stringResource(R.string.phone)) },
                                leadingIcon = { Icon(Icons.Default.Phone, contentDescription = null) },
                                modifier = Modifier.fillMaxWidth(),
                                singleLine = true
                            )
                            
                            Spacer(modifier = Modifier.height(8.dp))
                            
                            OutlinedTextField(
                                value = address,
                                onValueChange = { takeawayViewModel.setAddress(it) },
                                label = { Text(stringResource(R.string.address)) },
                                leadingIcon = { Icon(Icons.Default.LocationOn, contentDescription = null) },
                                modifier = Modifier.fillMaxWidth(),
                                minLines = 2
                            )
                            
                            Spacer(modifier = Modifier.height(16.dp))
                            
                            // Payment method selection
                            Text(
                                text = "Payment Method",
                                style = MaterialTheme.typography.titleMedium,
                                modifier = Modifier.padding(bottom = 8.dp)
                            )
                            
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                RadioButton(
                                    selected = paymentMethod == TakeawayPaymentMethod.CASH_ON_DELIVERY,
                                    onClick = { takeawayViewModel.setPaymentMethod(TakeawayPaymentMethod.CASH_ON_DELIVERY) }
                                )
                                Text(
                                    text = "Cash on Delivery",
                                    modifier = Modifier.clickable { 
                                        takeawayViewModel.setPaymentMethod(TakeawayPaymentMethod.CASH_ON_DELIVERY) 
                                    }
                                )
                                
                                Spacer(modifier = Modifier.width(16.dp))
                                
                                RadioButton(
                                    selected = paymentMethod == TakeawayPaymentMethod.ONLINE_PAYMENT,
                                    onClick = { takeawayViewModel.setPaymentMethod(TakeawayPaymentMethod.ONLINE_PAYMENT) }
                                )
                                Text(
                                    text = "Online Payment",
                                    modifier = Modifier.clickable { 
                                        takeawayViewModel.setPaymentMethod(TakeawayPaymentMethod.ONLINE_PAYMENT) 
                                    }
                                )
                            }
                            
                            Spacer(modifier = Modifier.height(8.dp))
                            
                            // Note field
                            OutlinedTextField(
                                value = note ?: "",
                                onValueChange = { takeawayViewModel.setNote(it) },
                                label = { Text("Note (Optional)") },
                                modifier = Modifier.fillMaxWidth(),
                                minLines = 2
                            )
                        }
                    }
                    
                    item {
                        Button(
                            onClick = {
                                takeawayViewModel.createOrder()
                            },
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 16.dp),
                            enabled = !isLoading && cartItems.isNotEmpty()
                        ) {
                            if (isLoading) {
                                CircularProgressIndicator(
                                    modifier = Modifier.size(24.dp),
                                    color = MaterialTheme.colorScheme.onPrimary,
                                    strokeWidth = 2.dp
                                )
                            } else {
                                Text(text = stringResource(R.string.place_order))
                            }
                        }
                    }
                    
                    item {
                        Spacer(modifier = Modifier.height(80.dp)) // Bottom padding for scrolling
                    }
                }
            }
            
            // Loading overlay
            if (isLoading) {
                LoadingIndicator()
            }
        }
    }
} 
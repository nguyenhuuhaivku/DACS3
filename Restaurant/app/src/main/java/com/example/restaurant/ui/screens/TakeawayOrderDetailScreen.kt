package com.example.restaurant.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Cancel
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.DeliveryDining
import androidx.compose.material.icons.filled.LocalShipping
import androidx.compose.material.icons.filled.LocationOn
import androidx.compose.material.icons.filled.Phone
import androidx.compose.material.icons.filled.Receipt
import androidx.compose.material.icons.filled.Update
import androidx.compose.material.icons.filled.AccountCircle
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.Divider
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TopAppBar
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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import coil.compose.AsyncImage
import com.example.restaurant.data.model.TakeawayOrder
import com.example.restaurant.data.model.TakeawayOrderItem
import com.example.restaurant.data.model.TakeawayOrderStatus
import com.example.restaurant.ui.components.LoadingIndicator
import com.example.restaurant.ui.theme.Gold
import com.example.restaurant.ui.viewmodel.TakeawayViewModel
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.text.SimpleDateFormat
import java.util.Locale

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TakeawayOrderDetailScreen(
    orderId: Int,
    onNavigateBack: () -> Unit = {},
    viewModel: TakeawayViewModel = viewModel()
) {
    val selectedOrder by viewModel.selectedOrder.observeAsState()
    val isLoading by viewModel.isLoading.observeAsState(false)
    val error by viewModel.error.observeAsState(null)
    
    val snackbarHostState = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    
    var showCancelConfirmation by remember { mutableStateOf(false) }
    
    // Load order details
    LaunchedEffect(orderId) {
        viewModel.loadOrder(orderId)
    }
    
    // Handle errors
    LaunchedEffect(error) {
        error?.let {
            scope.launch {
                snackbarHostState.showSnackbar(it)
                viewModel.clearError()
            }
        }
    }
    
    Scaffold(
        snackbarHost = { SnackbarHost(snackbarHostState) },
        topBar = {
            TopAppBar(
                title = { Text("Order Details") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    selectedOrder?.let { order ->
                        if (viewModel.canCancelOrder(order)) {
                            IconButton(onClick = { showCancelConfirmation = true }) {
                                Icon(Icons.Default.Cancel, contentDescription = "Cancel Order")
                            }
                        }
                    }
                }
            )
        }
    ) { paddingValues ->
        if (isLoading && selectedOrder == null) {
            LoadingIndicator()
        } else {
            selectedOrder?.let { order ->
                OrderDetailsContent(
                    order = order,
                    modifier = Modifier.padding(paddingValues)
                )
            } ?: run {
                // Order not found
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "Order not found",
                        style = MaterialTheme.typography.titleLarge
                    )
                }
            }
        }
        
        // Cancel confirmation dialog
        if (showCancelConfirmation) {
            AlertDialog(
                onDismissRequest = { showCancelConfirmation = false },
                title = { Text("Cancel Order") },
                text = { Text("Are you sure you want to cancel this order?") },
                confirmButton = {
                    Button(
                        onClick = {
                            showCancelConfirmation = false
                            selectedOrder?.id?.let { 
                                viewModel.cancelOrder(it)
                            }
                        }
                    ) {
                        Text("Yes, Cancel")
                    }
                },
                dismissButton = {
                    TextButton(onClick = { showCancelConfirmation = false }) {
                        Text("No")
                    }
                }
            )
        }
    }
}

@Composable
fun OrderDetailsContent(
    order: TakeawayOrder,
    modifier: Modifier = Modifier
) {
    val formatter = NumberFormat.getCurrencyInstance(Locale("vi", "VN"))
    val dateFormatter = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
    
    LazyColumn(
        modifier = modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        // Order Status Card
        item {
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(4.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    // Status icon
                    Box(
                        modifier = Modifier
                            .size(80.dp)
                            .background(
                                color = when (order.status) {
                                    TakeawayOrderStatus.PENDING -> MaterialTheme.colorScheme.primary.copy(alpha = 0.2f)
                                    TakeawayOrderStatus.CONFIRMED -> Gold.copy(alpha = 0.2f)
                                    TakeawayOrderStatus.IN_PREPARATION -> MaterialTheme.colorScheme.secondary.copy(alpha = 0.2f)
                                    TakeawayOrderStatus.OUT_FOR_DELIVERY -> MaterialTheme.colorScheme.tertiary.copy(alpha = 0.2f)
                                    TakeawayOrderStatus.DELIVERED -> Color.Green.copy(alpha = 0.2f)
                                    TakeawayOrderStatus.CANCELLED -> Color.Red.copy(alpha = 0.2f)
                                    else -> MaterialTheme.colorScheme.primary.copy(alpha = 0.2f)
                                },
                                shape = CircleShape
                            ),
                        contentAlignment = Alignment.Center
                    ) {
                        val icon = when (order.status) {
                            TakeawayOrderStatus.PENDING -> Icons.Default.Receipt
                            TakeawayOrderStatus.CONFIRMED -> Icons.Default.CheckCircle
                            TakeawayOrderStatus.IN_PREPARATION -> Icons.Default.Update
                            TakeawayOrderStatus.OUT_FOR_DELIVERY -> Icons.Default.LocalShipping
                            TakeawayOrderStatus.DELIVERED -> Icons.Default.DeliveryDining
                            TakeawayOrderStatus.CANCELLED -> Icons.Default.Cancel
                            else -> Icons.Default.Receipt
                        }
                        
                        val iconColor = when (order.status) {
                            TakeawayOrderStatus.PENDING -> MaterialTheme.colorScheme.primary
                            TakeawayOrderStatus.CONFIRMED -> Gold
                            TakeawayOrderStatus.IN_PREPARATION -> MaterialTheme.colorScheme.secondary
                            TakeawayOrderStatus.OUT_FOR_DELIVERY -> MaterialTheme.colorScheme.tertiary
                            TakeawayOrderStatus.DELIVERED -> Color.Green
                            TakeawayOrderStatus.CANCELLED -> Color.Red
                            else -> MaterialTheme.colorScheme.primary
                        }
                        
                        Icon(
                            imageVector = icon,
                            contentDescription = "Order Status",
                            tint = iconColor,
                            modifier = Modifier.size(40.dp)
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    // Order code
                    Text(
                        text = "Order #${order.orderCode}",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    Spacer(modifier = Modifier.height(8.dp))
                    
                    // Order status
                    Text(
                        text = order.status,
                        style = MaterialTheme.typography.titleLarge,
                        color = when (order.status) {
                            TakeawayOrderStatus.PENDING -> MaterialTheme.colorScheme.primary
                            TakeawayOrderStatus.CONFIRMED -> Gold
                            TakeawayOrderStatus.IN_PREPARATION -> MaterialTheme.colorScheme.secondary
                            TakeawayOrderStatus.OUT_FOR_DELIVERY -> MaterialTheme.colorScheme.tertiary
                            TakeawayOrderStatus.DELIVERED -> Color.Green
                            TakeawayOrderStatus.CANCELLED -> Color.Red
                            else -> MaterialTheme.colorScheme.primary
                        },
                        fontWeight = FontWeight.Bold
                    )
                    
                    Spacer(modifier = Modifier.height(8.dp))
                    
                    // Order date
                    Text(
                        text = "Created on ${dateFormatter.format(order.createdAt)}",
                        style = MaterialTheme.typography.bodyMedium,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                    
                    if (order.estimatedDeliveryTime != null) {
                        Spacer(modifier = Modifier.height(8.dp))
                        
                        Text(
                            text = "Estimated delivery: ${dateFormatter.format(order.estimatedDeliveryTime)}",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.primary
                        )
                    }
                }
            }
        }
        
        // Customer Information
        item {
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(2.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp)
                ) {
                    Text(
                        text = "Delivery Information",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    // Customer name
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Icon(
                            imageVector = Icons.Filled.AccountCircle,
                            contentDescription = "Customer",
                            tint = MaterialTheme.colorScheme.primary,
                            modifier = Modifier.size(24.dp)
                        )
                        
                        Spacer(modifier = Modifier.width(8.dp))
                        
                        Text(
                            text = order.customerName,
                            style = MaterialTheme.typography.bodyLarge
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(8.dp))
                    
                    // Phone
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Icon(
                            imageVector = Icons.Default.Phone,
                            contentDescription = "Phone",
                            tint = MaterialTheme.colorScheme.primary,
                            modifier = Modifier.size(24.dp)
                        )
                        
                        Spacer(modifier = Modifier.width(8.dp))
                        
                        Text(
                            text = order.phone,
                            style = MaterialTheme.typography.bodyLarge
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(8.dp))
                    
                    // Address
                    Row(
                        verticalAlignment = Alignment.Top,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Icon(
                            imageVector = Icons.Default.LocationOn,
                            contentDescription = "Address",
                            tint = MaterialTheme.colorScheme.primary,
                            modifier = Modifier.size(24.dp)
                        )
                        
                        Spacer(modifier = Modifier.width(8.dp))
                        
                        Text(
                            text = order.address,
                            style = MaterialTheme.typography.bodyLarge
                        )
                    }
                    
                    if (order.note != null && order.note.isNotEmpty()) {
                        Spacer(modifier = Modifier.height(16.dp))
                        
                        Text(
                            text = "Note:",
                            style = MaterialTheme.typography.bodyMedium,
                            fontWeight = FontWeight.Bold
                        )
                        
                        Text(
                            text = order.note,
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }
            }
        }
        
        // Order items
        item {
            Card(
                modifier = Modifier.fillMaxWidth(),
                elevation = CardDefaults.cardElevation(2.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp)
                ) {
                    Text(
                        text = "Order Items",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    order.items?.let { items ->
                        items.forEach { item ->
                            OrderItemRow(item)
                            Divider(modifier = Modifier.padding(vertical = 8.dp))
                        }
                    }
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    // Payment method
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(
                            text = "Payment Method:",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                        
                        Text(
                            text = order.paymentMethod,
                            style = MaterialTheme.typography.bodyMedium,
                            fontWeight = FontWeight.Bold
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(8.dp))
                    
                    // Payment status
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(
                            text = "Payment Status:",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                        
                        Text(
                            text = order.paymentStatus,
                            style = MaterialTheme.typography.bodyMedium,
                            fontWeight = FontWeight.Bold,
                            color = when (order.paymentStatus) {
                                "Paid" -> Color.Green
                                "Pending" -> Gold
                                "Refunded" -> Color.Red
                                else -> MaterialTheme.colorScheme.onSurface
                            }
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    // Total
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = "Total:",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold
                        )
                        
                        Text(
                            text = formatter.format(order.totalAmount),
                            style = MaterialTheme.typography.titleLarge,
                            color = MaterialTheme.colorScheme.primary,
                            fontWeight = FontWeight.Bold
                        )
                    }
                }
            }
        }
        
        // Spacer at the bottom
        item {
            Spacer(modifier = Modifier.height(32.dp))
        }
    }
}

@Composable
fun OrderItemRow(item: TakeawayOrderItem) {
    val formatter = NumberFormat.getCurrencyInstance(Locale("vi", "VN"))
    
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically
    ) {
        // Item image if available
        item.menuItem?.let { menuItem ->
            if (!menuItem.imageUrl.isNullOrEmpty()) {
                AsyncImage(
                    model = menuItem.getCompleteImageUrl(),
                    contentDescription = menuItem.name,
                    contentScale = ContentScale.Crop,
                    modifier = Modifier
                        .size(60.dp)
                        .clip(RoundedCornerShape(8.dp))
                )
            } else {
                Box(
                    modifier = Modifier
                        .size(60.dp)
                        .background(
                            color = MaterialTheme.colorScheme.surfaceVariant,
                            shape = RoundedCornerShape(8.dp)
                        ),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = item.menuItem.name.first().toString(),
                        style = MaterialTheme.typography.titleLarge
                    )
                }
            }
            
            Spacer(modifier = Modifier.width(12.dp))
        }
        
        // Item details
        Column(
            modifier = Modifier.weight(1f)
        ) {
            Text(
                text = item.menuItem?.name ?: "Item #${item.itemId}",
                style = MaterialTheme.typography.bodyLarge,
                fontWeight = FontWeight.Bold
            )
            
            Text(
                text = formatter.format(item.price),
                style = MaterialTheme.typography.bodyMedium,
                color = MaterialTheme.colorScheme.primary
            )
        }
        
        // Quantity
        Surface(
            modifier = Modifier
                .padding(start = 8.dp),
            shape = RoundedCornerShape(4.dp),
            color = MaterialTheme.colorScheme.surfaceVariant
        ) {
            Text(
                text = "x${item.quantity}",
                style = MaterialTheme.typography.bodyMedium,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
            )
        }
        
        // Item total
        Text(
            text = formatter.format(item.price * item.quantity),
            style = MaterialTheme.typography.bodyMedium,
            fontWeight = FontWeight.Bold,
            modifier = Modifier.padding(start = 16.dp),
            textAlign = TextAlign.End
        )
    }
} 
package com.example.restaurant.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
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
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.ArrowForward
import androidx.compose.material.icons.filled.DeliveryDining
import androidx.compose.material.icons.filled.Fastfood
import androidx.compose.material.icons.filled.ShoppingCart
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.LocalShipping
import androidx.compose.material.icons.filled.Cancel
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
import androidx.compose.material3.TopAppBar
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.livedata.observeAsState
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.restaurant.data.model.TakeawayOrder
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
fun TakeawayOrdersScreen(
    onNavigateToOrderDetail: (Int) -> Unit,
    onNavigateBack: () -> Unit = {},
    viewModel: TakeawayViewModel = viewModel()
) {
    val orders by viewModel.userOrders.observeAsState(emptyList())
    val isLoading by viewModel.isLoading.observeAsState(false)
    val error by viewModel.error.observeAsState(null)
    
    val snackbarHostState = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()
    
    // Load orders when screen is first displayed
    LaunchedEffect(Unit) {
        viewModel.loadUserOrders()
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
                title = { Text("My Orders") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        }
    ) { paddingValues ->
        if (isLoading && orders.isEmpty()) {
            LoadingIndicator()
        } else if (orders.isEmpty()) {
            // No orders yet
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
                        imageVector = Icons.Default.Fastfood,
                        contentDescription = null,
                        tint = MaterialTheme.colorScheme.primary.copy(alpha = 0.5f),
                        modifier = Modifier.size(80.dp)
                    )
                    
                    Spacer(modifier = Modifier.height(16.dp))
                    
                    Text(
                        text = "You don't have any orders yet",
                        style = MaterialTheme.typography.titleMedium,
                        textAlign = TextAlign.Center
                    )
                }
            }
        } else {
            LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(paddingValues)
                    .padding(horizontal = 16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                item {
                    Text(
                        text = "Your Takeaway Orders",
                        style = MaterialTheme.typography.headlineSmall,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(vertical = 16.dp)
                    )
                }
                
                items(orders) { order ->
                    OrderCard(
                        order = order,
                        onClick = { onNavigateToOrderDetail(order.id) }
                    )
                }
                
                item {
                    Spacer(modifier = Modifier.height(16.dp))
                }
            }
        }
    }
}

@Composable
fun OrderCard(
    order: TakeawayOrder,
    onClick: () -> Unit
) {
    val formatter = NumberFormat.getCurrencyInstance(Locale("vi", "VN"))
    val dateFormatter = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
    
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(onClick = onClick),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp)
        ) {
            // Order ID and Status
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Order ID
                Text(
                    text = "Order #${order.orderCode}",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold
                )
                
                // Status with icon
                Row(
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    StatusIndicator(status = order.status)
                    
                    Spacer(modifier = Modifier.width(8.dp))
                    
                    Text(
                        text = order.status,
                        style = MaterialTheme.typography.bodyMedium,
                        color = getStatusColor(order.status),
                        fontWeight = FontWeight.Bold
                    )
                }
            }
            
            Spacer(modifier = Modifier.height(8.dp))
            
            // Date
            Text(
                text = "Placed on: ${dateFormatter.format(order.createdAt)}",
                style = MaterialTheme.typography.bodyMedium,
                color = MaterialTheme.colorScheme.onSurfaceVariant
            )
            
            Spacer(modifier = Modifier.height(12.dp))
            Divider()
            Spacer(modifier = Modifier.height(12.dp))
            
            // Order summary - number of items
            val itemCount = order.items?.sumOf { it.quantity } ?: 0
            Text(
                text = "$itemCount item${if (itemCount != 1) "s" else ""}",
                style = MaterialTheme.typography.bodyMedium
            )
            
            // Order items (limited preview)
            order.items?.take(2)?.forEach { item ->
                Spacer(modifier = Modifier.height(8.dp))
                
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "• ${item.quantity}x",
                        style = MaterialTheme.typography.bodyMedium,
                        fontWeight = FontWeight.Bold
                    )
                    
                    Spacer(modifier = Modifier.width(8.dp))
                    
                    Text(
                        text = item.menuItem?.name ?: "Item #${item.itemId}",
                        style = MaterialTheme.typography.bodyMedium,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis,
                        modifier = Modifier.weight(1f)
                    )
                }
            }
            
            // If there are more items than shown
            if ((order.items?.size ?: 0) > 2) {
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = "+ ${(order.items?.size ?: 0) - 2} more item${if ((order.items?.size ?: 0) - 2 > 1) "s" else ""}",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
            }
            
            Spacer(modifier = Modifier.height(12.dp))
            
            // Total and View Details
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Total
                Column {
                    Text(
                        text = "Total:",
                        style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant
                    )
                    
                    Text(
                        text = formatter.format(order.totalAmount),
                        style = MaterialTheme.typography.titleMedium,
                        color = MaterialTheme.colorScheme.primary,
                        fontWeight = FontWeight.Bold
                    )
                }
                
                // View details button/icon
                Surface(
                    modifier = Modifier
                        .clip(MaterialTheme.shapes.small)
                        .clickable(onClick = onClick),
                    color = MaterialTheme.colorScheme.primaryContainer
                ) {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.padding(horizontal = 12.dp, vertical = 8.dp)
                    ) {
                        Text(
                            text = "View Details",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onPrimaryContainer,
                            fontWeight = FontWeight.Medium
                        )
                        
                        Spacer(modifier = Modifier.width(4.dp))
                        
                        Icon(
                            imageVector = Icons.Default.ArrowForward,
                            contentDescription = "View Details",
                            tint = MaterialTheme.colorScheme.onPrimaryContainer,
                            modifier = Modifier.size(16.dp)
                        )
                    }
                }
            }
        }
    }
}

@Composable
fun StatusIndicator(status: String) {
    val (color, icon) = when (status) {
        TakeawayOrderStatus.PENDING -> Pair(MaterialTheme.colorScheme.primary, Icons.Default.ShoppingCart)
        TakeawayOrderStatus.CONFIRMED -> Pair(Gold, Icons.Default.CheckCircle)
        TakeawayOrderStatus.IN_PREPARATION -> Pair(MaterialTheme.colorScheme.secondary, Icons.Default.Fastfood)
        TakeawayOrderStatus.OUT_FOR_DELIVERY -> Pair(MaterialTheme.colorScheme.tertiary, Icons.Default.LocalShipping)
        TakeawayOrderStatus.DELIVERED -> Pair(Color.Green, Icons.Default.DeliveryDining)
        TakeawayOrderStatus.CANCELLED -> Pair(Color.Red, Icons.Default.Cancel)
        else -> Pair(MaterialTheme.colorScheme.primary, Icons.Default.ShoppingCart)
    }
    
    Box(
        modifier = Modifier
            .size(24.dp)
            .background(color.copy(alpha = 0.2f), CircleShape),
        contentAlignment = Alignment.Center
    ) {
        Icon(
            imageVector = icon,
            contentDescription = status,
            tint = color,
            modifier = Modifier.size(16.dp)
        )
    }
}

@Composable
fun getStatusColor(status: String): Color {
    return when (status) {
        TakeawayOrderStatus.PENDING -> MaterialTheme.colorScheme.primary
        TakeawayOrderStatus.CONFIRMED -> Gold
        TakeawayOrderStatus.IN_PREPARATION -> MaterialTheme.colorScheme.secondary
        TakeawayOrderStatus.OUT_FOR_DELIVERY -> MaterialTheme.colorScheme.tertiary
        TakeawayOrderStatus.DELIVERED -> Color.Green
        TakeawayOrderStatus.CANCELLED -> Color.Red
        else -> MaterialTheme.colorScheme.primary
    }
} 
package com.example.restaurant.data.model

import com.google.gson.annotations.SerializedName
import java.util.Date

/**
 * Model for Takeaway Orders
 */
data class TakeawayOrder(
    @SerializedName("OrderID")
    val id: Int,
    
    @SerializedName("OrderCode")
    val orderCode: String,
    
    @SerializedName("UserID")
    val userId: Int,
    
    @SerializedName("CustomerName")
    val customerName: String,
    
    @SerializedName("Phone")
    val phone: String,
    
    @SerializedName("Address")
    val address: String,
    
    @SerializedName("TotalAmount")
    val totalAmount: Double,
    
    @SerializedName("Status")
    val status: String,
    
    @SerializedName("PaymentMethod")
    val paymentMethod: String,
    
    @SerializedName("PaymentStatus")
    val paymentStatus: String,
    
    @SerializedName("Note")
    val note: String?,
    
    @SerializedName("DeliveryTime")
    val deliveryTime: Date?,
    
    @SerializedName("EstimatedDeliveryTime")
    val estimatedDeliveryTime: Date?,
    
    @SerializedName("CreatedAt")
    val createdAt: String,
    
    @SerializedName("UpdatedAt")
    val updatedAt: String,
    
    @SerializedName("items")
    val items: List<TakeawayOrderItem>? = null
)

/**
 * Model for Takeaway Order Items
 */
data class TakeawayOrderItem(
    @SerializedName("OrderItemID")
    val id: Int,
    
    @SerializedName("OrderID")
    val orderId: Int,
    
    @SerializedName("ItemID")
    val itemId: Int,
    
    @SerializedName("Quantity")
    val quantity: Int,
    
    @SerializedName("Price")
    val price: Double,
    
    @SerializedName("item")
    val menuItem: MenuItem? = null
)

/**
 * Request model to create a new takeaway order
 */
data class TakeawayOrderRequest(
    val customer_name: String,
    val phone: String,
    val address: String,
    val payment_method: String = "Cash on Delivery",
    val note: String? = null,
    val items: List<TakeawayOrderItemRequest>
)

/**
 * Request model for items in a takeaway order
 */
data class TakeawayOrderItemRequest(
    val id: Int,
    val quantity: Int
)

/**
 * Status values for Takeaway Orders
 */
object TakeawayOrderStatus {
    const val PENDING = "Pending"
    const val CONFIRMED = "Confirmed"
    const val IN_PREPARATION = "In Preparation"
    const val OUT_FOR_DELIVERY = "Out for Delivery"
    const val DELIVERED = "Delivered"
    const val CANCELLED = "Cancelled"
}

/**
 * Payment Method values for Takeaway Orders
 */
object TakeawayPaymentMethod {
    const val CASH_ON_DELIVERY = "Cash on Delivery"
    const val ONLINE_PAYMENT = "Online Payment"
}

/**
 * Payment Status values for Takeaway Orders
 */
object TakeawayPaymentStatus {
    const val PENDING = "Pending"
    const val PAID = "Paid"
    const val REFUNDED = "Refunded"
} 
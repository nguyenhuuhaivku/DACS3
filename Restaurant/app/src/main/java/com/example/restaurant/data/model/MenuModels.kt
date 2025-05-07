package com.example.restaurant.data.model

import com.example.restaurant.data.api.RetrofitClient
import com.google.gson.annotations.SerializedName

data class ApiResponse<T>(
    val success: Boolean,
    val message: String? = null,
    val data: T? = null,
    val errors: Map<String, List<String>>? = null
) {
    override fun toString(): String {
        return "ApiResponse(success=$success, message=$message, data=${if (data != null) "not null" else "null"})"
    }
}

data class MenuCategory(
    @SerializedName("CategoryID") val id: Int,
    @SerializedName("CategoryName") val name: String,
    @SerializedName("Description") val description: String?,
    @SerializedName("CreatedAt") val createdAt: String?,
    @SerializedName("UpdatedAt") val updatedAt: String?
)

data class MenuItem(
    @SerializedName("ItemID") val id: Int,
    @SerializedName("CategoryID") val categoryId: Int,
    @SerializedName("ItemName") val name: String,
    @SerializedName("Price") val price: Double,
    @SerializedName("status") val status: String,
    @SerializedName("Description") val description: String?,
    @SerializedName("Available") val available: Int,
    @SerializedName("ImageURL") val imageUrl: String?,
    @SerializedName("CreatedAt") val createdAt: String?,
    @SerializedName("UpdatedAt") val updatedAt: String?,
    @SerializedName("category") val category: MenuCategory? = null
) {
    fun isAvailable(): Boolean = available > 0
    
    // Convert status value to a user-friendly label
    fun getStatusLabel(): String {
        return when(status) {
            "Món mới" -> "New"
            "Phổ biến" -> "Popular"
            "Đặc biệt" -> "Special"
            else -> "Regular"
        }
    }
    
    // Get the complete image URL with base URL prefixed if needed
    fun getCompleteImageUrl(): String? {
        return imageUrl?.let { url ->
            if (url.startsWith("http")) url else "${RetrofitClient.getBaseUrl()}/$url"
        }
    }
}

data class CartItem(
    @SerializedName("id") val id: Int,
    @SerializedName("user_id") val userId: Int,
    @SerializedName("item_id") val itemId: Int,
    @SerializedName("quantity") val quantity: Int,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?,
    @SerializedName("ReservationID") val reservationId: Int? = null,
    @SerializedName("item") val menuItem: MenuItem? = null
) {
    // Calculate subtotal for this cart item
    fun getSubtotal(): Double {
        return menuItem?.price?.times(quantity) ?: 0.0
    }
}

data class CartResponse(
    val items: List<CartItem>,
    val total: Double
) {
    // Calculate total cart value
    fun calculateTotal(): Double {
        return items.sumOf { it.getSubtotal() }
    }
}

// Specific response types
data class ReservationsResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("data") 
    val data: List<Reservation>
)

data class ReservationResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("data") 
    val data: Reservation
)

data class UserResponse(
    val user: User
)

data class MenuCategoriesResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("data") 
    val data: List<MenuCategory>
)

data class MenuItemsResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("data") 
    val data: List<MenuItem>
)

data class MenuItemResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("data") 
    val data: MenuItem
)

data class CartItemsResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("data") 
    val data: CartResponse
)

data class ErrorResponse(
    @SerializedName("success") 
    val success: Boolean,
    
    @SerializedName("message") 
    val message: String?,
    
    @SerializedName("errors") 
    val errors: Map<String, List<String>>?
) 
package com.example.restaurant.data.model

import com.google.gson.annotations.SerializedName

/**
 * Data class representing an item in a reservation
 */
data class ReservationItem(
    @SerializedName("ReservationItemID")
    val id: Long,
    @SerializedName("ReservationID")
    val reservationId: Int,
    @SerializedName("ItemID")
    val itemId: Int,
    @SerializedName("Quantity")
    val quantity: Int,
    @SerializedName("Price")
    val price: Double,
    @SerializedName("PaymentID")
    val paymentId: Int?,
    @SerializedName("created_at")
    val createdAt: String?,
    @SerializedName("updated_at")
    val updatedAt: String?,
    @SerializedName("is_initial_order")
    val isInitialOrder: Boolean,
    @SerializedName("menuItem")
    val menuItem: MenuItem? = null
) {
    fun getSubtotal(): Double {
        return price * quantity
    }
} 
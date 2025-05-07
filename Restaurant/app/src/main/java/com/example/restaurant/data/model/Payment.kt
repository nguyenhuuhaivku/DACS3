package com.example.restaurant.data.model

import com.google.gson.annotations.SerializedName

/**
 * Data class representing a payment for a reservation
 */
data class Payment(
    @SerializedName("PaymentID")
    val id: Int,
    @SerializedName("PaymentCode")
    val code: String?,
    @SerializedName("ReservationID")
    val reservationId: Int,
    @SerializedName("Amount")
    val amount: Double,
    @SerializedName("PaymentMethod")
    val paymentMethod: String,
    @SerializedName("Status")
    val status: String,
    @SerializedName("PaymentProof")
    val paymentProof: String?,
    @SerializedName("CreatedAt")
    val createdAt: String?,
    @SerializedName("UpdatedAt")
    val updatedAt: String?
) {
    fun getStatusColor(): Int {
        return when(status) {
            "Đã thanh toán" -> android.graphics.Color.GREEN
            "Chờ thanh toán" -> android.graphics.Color.YELLOW
            "Đã hủy", "Từ chối" -> android.graphics.Color.RED
            else -> android.graphics.Color.GRAY
        }
    }
} 
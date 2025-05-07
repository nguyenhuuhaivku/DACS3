package com.example.restaurant.data.model

import com.google.gson.annotations.SerializedName
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

/**
 * Data class representing a reservation in the restaurant
 */
data class Reservation(
    @SerializedName("ReservationID")
    val id: Int,
    @SerializedName("ReservationCode")
    val code: String?,
    @SerializedName("UserID")
    val userId: Int,
    @SerializedName("FullName")
    val fullName: String,
    @SerializedName("Phone")
    val phone: String,
    @SerializedName("TableID")
    val tableId: Int?,
    @SerializedName("GuestCount")
    val guestCount: Int,
    @SerializedName("ReservationDate")
    val reservationDate: String,
    @SerializedName("Status")
    val status: String,
    @SerializedName("Note")
    val note: String?,
    @SerializedName("CreatedAt")
    val createdAt: String?,
    @SerializedName("UpdatedAt")
    val updatedAt: String?,
    @SerializedName("CheckInTime")
    val checkInTime: String?,
    @SerializedName("CheckOutTime")
    val checkOutTime: String?,
    @SerializedName("reservationItems")
    val reservationItems: List<ReservationItem>? = null,
    @SerializedName("payment")
    val payment: Payment? = null,
    @SerializedName("table")
    val table: Table? = null
) {
    fun getFormattedDate(): String {
        try {
            val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
            val outputFormat = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
            val date = inputFormat.parse(reservationDate) ?: return reservationDate
            return outputFormat.format(date)
        } catch (e: Exception) {
            return reservationDate
        }
    }
    
    fun getStatusColor(): Int {
        return when(status) {
            "Đã xác nhận" -> android.graphics.Color.GREEN
            "Chờ xác nhận" -> android.graphics.Color.YELLOW
            "Đã hủy" -> android.graphics.Color.RED
            "Đã hoàn tất" -> android.graphics.Color.BLUE
            else -> android.graphics.Color.GRAY
        }
    }
    
    fun getTotalAmount(): Double {
        return reservationItems?.sumOf { it.price * it.quantity } ?: 0.0
    }
} 
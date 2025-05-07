package com.example.restaurant.data.model

import com.google.gson.annotations.SerializedName

data class Table(
    @SerializedName("TableID")
    val id: Int,
    @SerializedName("TableNumber")
    val tableNumber: String,
    @SerializedName("Status")
    val status: String,
    @SerializedName("Seats")
    val seats: Int,
    @SerializedName("Location")
    val location: String,
    @SerializedName("created_at")
    val createdAt: String?,
    @SerializedName("updated_at")
    val updatedAt: String?
)
package com.example.restaurant.data.repository

import android.content.Context
import android.util.Log
import com.example.restaurant.data.api.ReservationRequest
import com.example.restaurant.data.api.RetrofitClient
import com.example.restaurant.data.model.Reservation

class ReservationRepository(private val context: Context) {
    private val TAG = "ReservationRepository"
    private val apiService = RetrofitClient.apiService
    
    /**
     * Get all reservations for the authenticated user
     */
    suspend fun getReservations(token: String): List<Reservation> {
        try {
            // Token already includes "Bearer " prefix from AuthRepository
            val response = apiService.getReservations(token)
            
            if (response.success) {
                return response.data ?: emptyList()
            } else {
                Log.e(TAG, "Error fetching reservations: ${response.message}")
                throw Exception(response.message ?: "Không thể tải danh sách đặt bàn")
            }
        } catch (e: Exception) {
            Log.e(TAG, "Exception fetching reservations: ${e.message}", e)
            throw e
        }
    }
    
    /**
     * Get a specific reservation by ID
     */
    suspend fun getReservation(token: String, id: Int): Reservation {
        try {
            // Token already includes "Bearer " prefix from AuthRepository
            val response = apiService.getReservation(token, id)
            
            if (response.success) {
                val data = response.data
                if (data != null) {
                    // The response returns a Map with reservation, items, etc.
                    // We need to extract the reservation from this map
                    @Suppress("UNCHECKED_CAST")
                    val reservationData = data["reservation"] as? Map<String, Any>
                    
                    if (reservationData != null) {
                        // Convert the map to Reservation object
                        // This is a simplified approach - in a real app, you might want to use Gson
                        val reservation = Reservation(
                            id = (reservationData["ReservationID"] as? Double)?.toInt() ?: 0,
                            code = reservationData["ReservationCode"] as? String,
                            userId = (reservationData["UserID"] as? Double)?.toInt() ?: 0,
                            fullName = reservationData["FullName"] as String,
                            phone = reservationData["Phone"] as String,
                            tableId = (reservationData["TableID"] as? Double)?.toInt(),
                            guestCount = (reservationData["GuestCount"] as? Double)?.toInt() ?: 0,
                            reservationDate = reservationData["ReservationDate"] as String,
                            status = reservationData["Status"] as String,
                            note = reservationData["Note"] as? String,
                            createdAt = reservationData["CreatedAt"] as? String,
                            updatedAt = reservationData["UpdatedAt"] as? String,
                            checkInTime = reservationData["CheckInTime"] as? String,
                            checkOutTime = reservationData["CheckOutTime"] as? String
                        )
                        return reservation
                    }
                }
                throw Exception("Lỗi khi xử lý dữ liệu đặt bàn")
            } else {
                Log.e(TAG, "Error fetching reservation: ${response.message}")
                throw Exception(response.message ?: "Không thể tải chi tiết đặt bàn")
            }
        } catch (e: Exception) {
            Log.e(TAG, "Exception fetching reservation: ${e.message}", e)
            throw e
        }
    }
    
    /**
     * Create a new reservation
     */
    suspend fun createReservation(token: String, request: ReservationRequest): Reservation {
        try {
            // Log the request for debugging
            Log.d(TAG, "Creating reservation with request: $request")
            Log.d(TAG, "Authorization token: ${token.take(15)}...")
            
            // Ensure token format is correct (should include "Bearer " prefix)
            val tokenToUse = if (!token.startsWith("Bearer ")) {
                "Bearer $token"
            } else {
                token
            }
            
            val response = apiService.createReservation(tokenToUse, request)
            
            if (response.success) {
                Log.d(TAG, "Reservation created successfully")
                return response.data 
                    ?: throw Exception("Đặt bàn thành công nhưng không nhận được dữ liệu phản hồi")
            } else {
                Log.e(TAG, "Error creating reservation: ${response.message}")
                throw Exception(response.message ?: "Không thể tạo đặt bàn")
            }
        } catch (e: Exception) {
            Log.e(TAG, "Exception creating reservation: ${e.message}", e)
            throw e
        }
    }
    
    /**
     * Cancel a reservation
     */
    suspend fun cancelReservation(token: String, id: Int) {
        try {
            // Token already includes "Bearer " prefix from AuthRepository
            val response = apiService.cancelReservation(token, id)
            
            if (!response.success) {
                Log.e(TAG, "Error canceling reservation: ${response.message}")
                throw Exception(response.message ?: "Không thể hủy đặt bàn")
            }
        } catch (e: Exception) {
            Log.e(TAG, "Exception canceling reservation: ${e.message}", e)
            throw e
        }
    }
} 
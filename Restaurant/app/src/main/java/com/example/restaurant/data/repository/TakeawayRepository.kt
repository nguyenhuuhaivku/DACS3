package com.example.restaurant.data.repository

import android.content.Context
import android.util.Log
import com.example.restaurant.data.api.RetrofitClient
import com.example.restaurant.data.model.TakeawayOrder
import com.example.restaurant.data.model.TakeawayOrderRequest
import com.example.restaurant.data.model.TakeawayOrderItemRequest
import com.example.restaurant.ui.viewmodel.CartItem
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class TakeawayRepository(private val context: Context) {
    private val TAG = "TakeawayRepository"
    private val apiService = RetrofitClient.apiService
    private val authRepository = AuthRepository(context)
    
    /**
     * Get all takeaway orders for the current user
     */
    suspend fun getUserTakeawayOrders(): Result<List<TakeawayOrder>> = withContext(Dispatchers.IO) {
        try {
            val token = authRepository.getAuthToken() ?: return@withContext Result.failure(Exception("User not authenticated"))
            val response = apiService.getTakeawayOrders("Bearer $token")
            
            if (response.success) {
                Result.success(response.data ?: emptyList())
            } else {
                Result.failure(Exception(response.message ?: "Failed to get takeaway orders"))
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting takeaway orders: ${e.message}", e)
            Result.failure(e)
        }
    }
    
    /**
     * Get a specific takeaway order by ID
     */
    suspend fun getTakeawayOrder(orderId: Int): Result<TakeawayOrder> = withContext(Dispatchers.IO) {
        try {
            val token = authRepository.getAuthToken() ?: return@withContext Result.failure(Exception("User not authenticated"))
            val response = apiService.getTakeawayOrder("Bearer $token", orderId)
            
            if (response.success) {
                response.data?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Order data not found"))
            } else {
                Result.failure(Exception(response.message ?: "Failed to get takeaway order"))
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting takeaway order: ${e.message}", e)
            Result.failure(e)
        }
    }
    
    /**
     * Create a new takeaway order from cart items
     */
    suspend fun createTakeawayOrder(
        customerName: String,
        phone: String,
        address: String,
        paymentMethod: String,
        note: String?,
        cartItems: List<CartItem>
    ): Result<TakeawayOrder> = withContext(Dispatchers.IO) {
        try {
            val token = authRepository.getAuthToken() ?: return@withContext Result.failure(Exception("User not authenticated"))
            
            // Convert cart items to order item requests
            val orderItems = cartItems.map { cartItem ->
                TakeawayOrderItemRequest(
                    id = cartItem.id,
                    quantity = cartItem.quantity
                )
            }
            
            // Create the request object
            val orderRequest = TakeawayOrderRequest(
                customer_name = customerName,
                phone = phone,
                address = address,
                payment_method = paymentMethod,
                note = note,
                items = orderItems
            )
            
            val response = apiService.createTakeawayOrder("Bearer $token", orderRequest)
            
            if (response.success) {
                response.data?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Order data not returned after creation"))
            } else {
                Result.failure(Exception(response.message ?: "Failed to create takeaway order"))
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error creating takeaway order: ${e.message}", e)
            Result.failure(e)
        }
    }
    
    /**
     * Cancel a takeaway order
     */
    suspend fun cancelTakeawayOrder(orderId: Int): Result<TakeawayOrder> = withContext(Dispatchers.IO) {
        try {
            val token = authRepository.getAuthToken() ?: return@withContext Result.failure(Exception("User not authenticated"))
            val response = apiService.cancelTakeawayOrder("Bearer $token", orderId)
            
            if (response.success) {
                response.data?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Order data not returned after cancellation"))
            } else {
                Result.failure(Exception(response.message ?: "Failed to cancel takeaway order"))
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error cancelling takeaway order: ${e.message}", e)
            Result.failure(e)
        }
    }
    
    /**
     * Get the tracking information for a takeaway order
     */
    suspend fun getTakeawayOrderTracking(orderId: Int): Result<List<Map<String, Any>>> = withContext(Dispatchers.IO) {
        try {
            val token = authRepository.getAuthToken() ?: return@withContext Result.failure(Exception("User not authenticated"))
            val response = apiService.getTakeawayOrderTracking("Bearer $token", orderId)
            
            if (response.success) {
                Result.success(response.data ?: emptyList())
            } else {
                Result.failure(Exception(response.message ?: "Failed to get order tracking"))
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting order tracking: ${e.message}", e)
            Result.failure(e)
        }
    }
} 
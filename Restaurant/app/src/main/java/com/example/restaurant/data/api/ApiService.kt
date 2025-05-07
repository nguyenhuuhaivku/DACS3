package com.example.restaurant.data.api

import com.example.restaurant.data.model.ApiResponse
import com.example.restaurant.data.model.CartResponse
import com.example.restaurant.data.model.MenuCategory
import com.example.restaurant.data.model.MenuItem
import com.example.restaurant.data.model.AuthResponse
import com.example.restaurant.data.model.Reservation
import com.example.restaurant.data.model.User
import com.example.restaurant.data.model.TakeawayOrder
import com.example.restaurant.data.model.TakeawayOrderRequest
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path
import retrofit2.http.Query

interface ApiService {
    @GET("menu-categories")
    suspend fun getMenuCategories(): ApiResponse<List<MenuCategory>>

    @GET("menu-items")
    suspend fun getAllMenuItems(): ApiResponse<List<MenuItem>>

    @GET("menu-items/{id}")
    suspend fun getMenuItem(@Path("id") id: Int): ApiResponse<MenuItem>

    @GET("menu-items/category/{categoryId}")
    suspend fun getMenuItemsByCategory(@Path("categoryId") categoryId: Int): ApiResponse<Map<String, Any>>

    @GET("cart")
    suspend fun getCart(@Header("Authorization") token: String): CartResponse

    @POST("cart")
    suspend fun addToCart(
        @Header("Authorization") token: String,
        @Query("item_id") itemId: Int, 
        @Query("quantity") quantity: Int
    ): CartResponse

    /**
     * Login endpoint
     * Expected response format:
     * {
     *    "success": true,
     *    "data": {
     *       "user": { ... },
     *       "access_token": "token_string",
     *       "token_type": "Bearer"
     *    }
     * }
     */
    @FormUrlEncoded
    @POST("login")
    suspend fun login(
        @Field("email") email: String,
        @Field("password") password: String
    ): AuthResponse

    /**
     * Register endpoint
     * Expected response format:
     * {
     *    "success": true,
     *    "data": {
     *       "user": { ... },
     *       "access_token": "token_string",
     *       "token_type": "Bearer"
     *    }
     * }
     */
    @FormUrlEncoded
    @POST("register")
    suspend fun register(
        @Field("name") name: String,
        @Field("email") email: String,
        @Field("password") password: String
    ): AuthResponse

    @GET("user")
    suspend fun getCurrentUser(@Header("Authorization") token: String): ApiResponse<User>

    @POST("logout")
    suspend fun logout(@Header("Authorization") token: String): ApiResponse<Nothing>
    
    /**
     * Reservation endpoints
     */
    @GET("reservations")
    suspend fun getReservations(@Header("Authorization") token: String): ApiResponse<List<Reservation>>
    
    @GET("reservations/{id}")
    suspend fun getReservation(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): ApiResponse<Map<String, Any>>
    
    @POST("reservations")
    suspend fun createReservation(
        @Header("Authorization") token: String,
        @Body reservationRequest: ReservationRequest
    ): ApiResponse<Reservation>
    
    @PUT("reservations/{id}/cancel")
    suspend fun cancelReservation(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): ApiResponse<Nothing>
    
    /**
     * Delete item from cart
     */
    @DELETE("cart/{id}")
    suspend fun removeFromCart(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): CartResponse
    
    /**
     * Update item quantity in cart
     */
    @PUT("cart/{id}")
    suspend fun updateCartItem(
        @Header("Authorization") token: String,
        @Path("id") id: Int,
        @Query("quantity") quantity: Int
    ): CartResponse

    /**
     * Takeaway Order endpoints
     */
    @GET("takeaway-orders")
    suspend fun getTakeawayOrders(@Header("Authorization") token: String): ApiResponse<List<TakeawayOrder>>
    
    @GET("takeaway-orders/{id}")
    suspend fun getTakeawayOrder(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): ApiResponse<TakeawayOrder>
    
    @POST("takeaway-orders")
    suspend fun createTakeawayOrder(
        @Header("Authorization") token: String,
        @Body takeawayOrderRequest: TakeawayOrderRequest
    ): ApiResponse<TakeawayOrder>
    
    @PUT("takeaway-orders/{id}/cancel")
    suspend fun cancelTakeawayOrder(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): ApiResponse<TakeawayOrder>
    
    @GET("takeaway-orders/{id}/tracking")
    suspend fun getTakeawayOrderTracking(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): ApiResponse<List<Map<String, Any>>>
}

/**
 * Data class for creating a reservation
 */
data class ReservationRequest(
    val guest_count: Int,
    val reservation_date: String,
    val phone: String,
    val note: String? = null,
    val items: List<ReservationItemRequest>? = null
)

/**
 * Data class for items in a reservation request
 */
data class ReservationItemRequest(
    val id: Int,
    val quantity: Int
) 
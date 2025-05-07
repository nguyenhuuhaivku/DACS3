package com.example.restaurant.ui.viewmodel

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.restaurant.R
import com.example.restaurant.data.model.MenuItem
import com.example.restaurant.data.repository.AuthRepository
import com.example.restaurant.data.repository.CartRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException

data class CartItem(
    val id: Int,
    val name: String,
    val price: Double,
    val imageUrl: String? = null,
    val imageRes: Int = R.drawable.placeholder_food,
    val quantity: Int = 1
)

enum class OrderStatus {
    NONE,
    PROCESSING,
    CONFIRMED,
    PREPARING,
    READY,
    DELIVERED,
    CANCELLED
}

class CartViewModel(application: Application) : AndroidViewModel(application) {
    private val cartRepository = CartRepository(application.applicationContext)
    private val authRepository = AuthRepository(application.applicationContext)
    
    private val _cartItems = MutableLiveData<List<CartItem>>(emptyList())
    val cartItems: LiveData<List<CartItem>> = _cartItems
    
    private val _cartTotal = MutableLiveData(0.0)
    val cartTotal: LiveData<Double> = _cartTotal
    
    private val _cartItemCount = MutableLiveData(0)
    val cartItemCount: LiveData<Int> = _cartItemCount
    
    private val _deliveryFee = MutableLiveData(2.99)
    val deliveryFee: LiveData<Double> = _deliveryFee
    
    private val _isLoading = MutableLiveData(false)
    val isLoading: LiveData<Boolean> = _isLoading
    
    private val _error = MutableLiveData<String?>(null)
    val error: LiveData<String?> = _error
    
    private val _orderStatus = MutableLiveData(OrderStatus.NONE)
    val orderStatus: LiveData<OrderStatus> = _orderStatus
    
    init {
        // Load cart items from persistent storage on initialization
        viewModelScope.launch {
            loadCartItems()
        }
    }
    
    fun loadCartItems() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                withContext(Dispatchers.IO) {
                    val userId = authRepository.getCurrentUser()?.id
                    val items = cartRepository.getCartItems(userId)
                    _cartItems.postValue(items)
                    updateCartTotals(items)
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun addToCart(menuItem: MenuItem) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                withContext(Dispatchers.IO) {
                    val userId = authRepository.getCurrentUser()?.id
                    val updatedItems = cartRepository.addToCart(menuItem, userId)
                    _cartItems.postValue(updatedItems)
                    updateCartTotals(updatedItems)
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun updateItemQuantity(itemId: Int, newQuantity: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                withContext(Dispatchers.IO) {
                    val userId = authRepository.getCurrentUser()?.id
                    val updatedItems = cartRepository.updateItemQuantity(itemId, newQuantity, userId)
                    _cartItems.postValue(updatedItems)
                    updateCartTotals(updatedItems)
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun removeItem(itemId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                withContext(Dispatchers.IO) {
                    val userId = authRepository.getCurrentUser()?.id
                    val updatedItems = cartRepository.removeItem(itemId, userId)
                    _cartItems.postValue(updatedItems)
                    updateCartTotals(updatedItems)
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun clearCart() {
        // Immediately update UI state to empty
        _cartItems.value = emptyList()
        _cartTotal.value = 0.0
        _cartItemCount.value = 0
        
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                withContext(Dispatchers.IO) {
                    val userId = authRepository.getCurrentUser()?.id
                    cartRepository.clearCart(userId)
                }
            } catch (e: Exception) {
                handleError(e)
                // Re-load cart items if there was an error clearing the cart
                loadCartItems()
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun transferAnonymousCartToUser() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                withContext(Dispatchers.IO) {
                    val userId = authRepository.getCurrentUser()?.id
                    if (userId != null) {
                        val items = cartRepository.transferAnonymousCartToUser(userId)
                        _cartItems.postValue(items)
                        updateCartTotals(items)
                    }
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun placeOrder(deliveryAddress: String, phoneNumber: String) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                _orderStatus.value = OrderStatus.PROCESSING
                
                // Simulate network delay for order processing
                withContext(Dispatchers.IO) {
                    // In a real app, this would make an API call to place the order
                    kotlinx.coroutines.delay(1500)
                    _orderStatus.postValue(OrderStatus.CONFIRMED)
                    
                    // Clear the cart after successful order
                    val userId = authRepository.getCurrentUser()?.id
                    val emptyList = cartRepository.clearCart(userId)
                    _cartItems.postValue(emptyList)
                    updateCartTotals(emptyList)
                }
            } catch (e: Exception) {
                handleError(e)
                _orderStatus.value = OrderStatus.CANCELLED
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun updateDeliveryFee(subtotal: Double) {
        // Calculate delivery fee based on order subtotal
        // Free delivery for orders over $35
        val fee = when {
            subtotal >= 35.0 -> 0.0
            subtotal >= 25.0 -> 1.99
            subtotal >= 15.0 -> 2.99
            else -> 3.99
        }
        _deliveryFee.value = fee
    }
    
    fun clearError() {
        _error.value = null
    }
    
    fun resetOrderStatus() {
        _orderStatus.value = OrderStatus.NONE
    }
    
    private fun updateCartTotals(items: List<CartItem>) {
        val total = items.sumOf { it.price * it.quantity }
        val count = items.sumOf { it.quantity }
        
        _cartTotal.postValue(total)
        _cartItemCount.postValue(count)
        updateDeliveryFee(total)
    }
    
    /**
     * Reset cart item count to zero
     * This is used to ensure the badge count is updated correctly
     * after operations like clearing the cart
     */
    fun resetCartItemCount() {
        _cartItemCount.value = 0
    }
    
    private fun handleError(e: Exception) {
        val errorMsg = when (e) {
            is UnknownHostException -> "No internet connection"
            is ConnectException -> "Failed to connect to server"
            is SocketTimeoutException -> "Connection timed out"
            else -> "An error occurred: ${e.message}"
        }
        _error.postValue(errorMsg)
    }
} 
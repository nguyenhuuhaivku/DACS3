package com.example.restaurant.ui.viewmodel

import android.app.Application
import android.util.Log
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.restaurant.data.model.TakeawayOrder
import com.example.restaurant.data.model.TakeawayOrderStatus
import com.example.restaurant.data.model.TakeawayPaymentMethod
import com.example.restaurant.data.repository.TakeawayRepository
import com.example.restaurant.data.repository.CartRepository
import com.example.restaurant.data.repository.AuthRepository
import kotlinx.coroutines.launch

class TakeawayViewModel(application: Application) : AndroidViewModel(application) {
    private val TAG = "TakeawayViewModel"
    
    // Repositories
    private val takeawayRepository = TakeawayRepository(application)
    private val cartRepository = CartRepository(application)
    private val authRepository = AuthRepository(application)
    
    // LiveData for takeaway orders
    private val _userOrders = MutableLiveData<List<TakeawayOrder>>()
    val userOrders: LiveData<List<TakeawayOrder>> = _userOrders
    
    // LiveData for a selected order
    private val _selectedOrder = MutableLiveData<TakeawayOrder?>()
    val selectedOrder: LiveData<TakeawayOrder?> = _selectedOrder
    
    // LiveData for order tracking
    private val _orderTracking = MutableLiveData<List<Map<String, Any>>>()
    val orderTracking: LiveData<List<Map<String, Any>>> = _orderTracking
    
    // State handling
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error
    
    private val _orderSuccess = MutableLiveData<Boolean>()
    val orderSuccess: LiveData<Boolean> = _orderSuccess
    
    private val _cartCleared = MutableLiveData<Boolean>()
    val cartCleared: LiveData<Boolean> = _cartCleared
    
    // Last created order ID (for navigation)
    private val _lastOrderId = MutableLiveData<Int?>()
    val lastOrderId: LiveData<Int?> = _lastOrderId
    
    // Form state
    private val _customerName = MutableLiveData<String>()
    val customerName: LiveData<String> = _customerName
    
    private val _phone = MutableLiveData<String>()
    val phone: LiveData<String> = _phone
    
    private val _address = MutableLiveData<String>()
    val address: LiveData<String> = _address
    
    private val _note = MutableLiveData<String?>()
    val note: LiveData<String?> = _note
    
    private val _paymentMethod = MutableLiveData<String>(TakeawayPaymentMethod.CASH_ON_DELIVERY)
    val paymentMethod: LiveData<String> = _paymentMethod
    
    // Initialize with user data if available
    init {
        val currentUser = authRepository.getCurrentUser()
        if (currentUser != null) {
            _customerName.value = currentUser.name
        }
    }
    
    /**
     * Set form values
     */
    fun setCustomerName(name: String) {
        _customerName.value = name
    }
    
    fun setPhone(phone: String) {
        _phone.value = phone
    }
    
    fun setAddress(address: String) {
        _address.value = address
    }
    
    fun setNote(note: String?) {
        _note.value = note
    }
    
    fun setPaymentMethod(method: String) {
        _paymentMethod.value = method
    }
    
    /**
     * Load user's takeaway orders
     */
    fun loadUserOrders() {
        _isLoading.value = true
        _error.value = null
        
        viewModelScope.launch {
            takeawayRepository.getUserTakeawayOrders()
                .onSuccess { orders ->
                    _userOrders.value = orders
                }
                .onFailure { exception ->
                    _error.value = exception.message
                    Log.e(TAG, "Error loading user orders", exception)
                }
            
            _isLoading.value = false
        }
    }
    
    /**
     * Get a specific takeaway order
     */
    fun loadOrder(orderId: Int) {
        _isLoading.value = true
        _error.value = null
        
        viewModelScope.launch {
            takeawayRepository.getTakeawayOrder(orderId)
                .onSuccess { order ->
                    _selectedOrder.value = order
                }
                .onFailure { exception ->
                    _error.value = exception.message
                    Log.e(TAG, "Error loading order $orderId", exception)
                }
            
            _isLoading.value = false
        }
    }
    
    /**
     * Load order tracking data
     */
    fun loadOrderTracking(orderId: Int) {
        _isLoading.value = true
        _error.value = null
        
        viewModelScope.launch {
            takeawayRepository.getTakeawayOrderTracking(orderId)
                .onSuccess { tracking ->
                    _orderTracking.value = tracking
                }
                .onFailure { exception ->
                    _error.value = exception.message
                    Log.e(TAG, "Error loading order tracking for order $orderId", exception)
                }
            
            _isLoading.value = false
        }
    }
    
    /**
     * Create a new takeaway order with current cart items
     */
    fun createOrder() {
        val currentName = _customerName.value
        val currentPhone = _phone.value
        val currentAddress = _address.value
        val currentPaymentMethod = _paymentMethod.value
        val currentNote = _note.value
        
        // Validate required fields
        if (currentName.isNullOrEmpty() || currentPhone.isNullOrEmpty() || currentAddress.isNullOrEmpty()) {
            _error.value = "Please fill in all required fields"
            return
        }
        
        // Get current user ID
        val userId = authRepository.getCurrentUser()?.id
        if (userId == null) {
            _error.value = "You must be logged in to place an order"
            return
        }
        
        // Get cart items
        val cartItems = cartRepository.getCartItems(userId)
        if (cartItems.isEmpty()) {
            _error.value = "Your cart is empty"
            return
        }
        
        _isLoading.value = true
        _error.value = null
        _orderSuccess.value = false
        
        viewModelScope.launch {
            takeawayRepository.createTakeawayOrder(
                customerName = currentName,
                phone = currentPhone,
                address = currentAddress,
                paymentMethod = currentPaymentMethod ?: TakeawayPaymentMethod.CASH_ON_DELIVERY,
                note = currentNote,
                cartItems = cartItems
            )
                .onSuccess { order ->
                    // Clear the cart after successful order
                    cartRepository.clearCart(userId)
                    _cartCleared.value = true
                    
                    // Set success state
                    _orderSuccess.value = true
                    _lastOrderId.value = order.id
                    _selectedOrder.value = order
                }
                .onFailure { exception ->
                    _error.value = exception.message
                    Log.e(TAG, "Error creating takeaway order", exception)
                }
            
            _isLoading.value = false
        }
    }
    
    /**
     * Cancel a takeaway order
     */
    fun cancelOrder(orderId: Int) {
        _isLoading.value = true
        _error.value = null
        
        viewModelScope.launch {
            takeawayRepository.cancelTakeawayOrder(orderId)
                .onSuccess { order ->
                    // Update the selected order with the cancelled one
                    _selectedOrder.value = order
                    
                    // Refresh the orders list
                    loadUserOrders()
                }
                .onFailure { exception ->
                    _error.value = exception.message
                    Log.e(TAG, "Error cancelling order $orderId", exception)
                }
            
            _isLoading.value = false
        }
    }
    
    /**
     * Check if an order can be cancelled based on its status
     */
    fun canCancelOrder(order: TakeawayOrder): Boolean {
        return order.status == TakeawayOrderStatus.PENDING || 
               order.status == TakeawayOrderStatus.CONFIRMED
    }
    
    /**
     * Reset form and state after order
     */
    fun resetAfterOrder() {
        _orderSuccess.value = false
        _cartCleared.value = false
        _lastOrderId.value = null
        _note.value = null
        _address.value = null
    }
    
    /**
     * Clear error message
     */
    fun clearError() {
        _error.value = null
    }
} 
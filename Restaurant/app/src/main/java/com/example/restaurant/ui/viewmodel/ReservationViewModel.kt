package com.example.restaurant.ui.viewmodel

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.restaurant.data.api.ReservationItemRequest
import com.example.restaurant.data.api.ReservationRequest
import com.example.restaurant.data.model.Reservation
import com.example.restaurant.data.repository.AuthRepository
import com.example.restaurant.data.repository.ReservationRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Date
import java.util.Locale

class ReservationViewModel(application: Application) : AndroidViewModel(application) {
    private val reservationRepository = ReservationRepository(application.applicationContext)
    private val authRepository = AuthRepository(application.applicationContext)
    
    private val _reservations = MutableLiveData<List<Reservation>>(emptyList())
    val reservations: LiveData<List<Reservation>> = _reservations
    
    private val _currentReservation = MutableLiveData<Reservation?>(null)
    val currentReservation: LiveData<Reservation?> = _currentReservation
    
    private val _isLoading = MutableLiveData(false)
    val isLoading: LiveData<Boolean> = _isLoading
    
    private val _success = MutableLiveData<String?>(null)
    val success: LiveData<String?> = _success
    
    private val _error = MutableLiveData<String?>(null)
    val error: LiveData<String?> = _error
    
    fun loadReservations() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val authToken = authRepository.getAuthToken()
                if (authToken != null) {
                    val result = reservationRepository.getReservations(authToken)
                    _reservations.value = result
                } else {
                    _error.value = "Vui lòng đăng nhập để xem đơn đặt bàn"
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun getReservation(id: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val authToken = authRepository.getAuthToken()
                if (authToken != null) {
                    val result = reservationRepository.getReservation(authToken, id)
                    _currentReservation.value = result
                } else {
                    _error.value = "Vui lòng đăng nhập để xem chi tiết đơn đặt bàn"
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun createReservation(
        name: String,
        phone: String,
        guestCount: Int,
        reservationDate: Date,
        note: String?,
        cartItems: List<CartItem>,
        cartViewModel: CartViewModel? = null
    ) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                _success.value = null
                
                val authToken = authRepository.getAuthToken()
                if (authToken != null) {
                    // Format the date and time correctly for the API
                    val dateFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                    val formattedDateTime = dateFormat.format(reservationDate)
                    
                    // Convert cart items to reservation item requests
                    val reservationItems = cartItems.map { 
                        ReservationItemRequest(id = it.id, quantity = it.quantity) 
                    }
                    
                    // Create reservation request
                    val reservationRequest = ReservationRequest(
                        guest_count = guestCount,
                        reservation_date = formattedDateTime,
                        phone = phone,
                        note = note,
                        items = reservationItems
                    )
                    
                    // Debug log to check request
                    android.util.Log.d("ReservationViewModel", "Sending reservation request: $reservationRequest")
                    
                    // Submit the reservation
                    val result = reservationRepository.createReservation(authToken, reservationRequest)
                    
                    // Update the current reservation with the result
                    _currentReservation.value = result
                    
                    // Clear cart immediately if cartViewModel is provided
                    cartViewModel?.clearCart()
                    
                    // Show success message
                    _success.value = "Đặt bàn thành công! Mã đặt bàn: ${result.code}"
                } else {
                    _error.value = "Vui lòng đăng nhập để đặt bàn"
                }
            } catch (e: Exception) {
                android.util.Log.e("ReservationViewModel", "Error creating reservation", e)
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun cancelReservation(id: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                _success.value = null
                
                val authToken = authRepository.getAuthToken()
                if (authToken != null) {
                    reservationRepository.cancelReservation(authToken, id)
                    
                    // Reload reservations after cancellation
                    loadReservations()
                    
                    // Show success message
                    _success.value = "Đã hủy đơn đặt bàn thành công"
                } else {
                    _error.value = "Vui lòng đăng nhập để hủy đơn đặt bàn"
                }
            } catch (e: Exception) {
                handleError(e)
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun clearMessages() {
        _error.value = null
        _success.value = null
    }
    
    /**
     * Clear cart and update UI after successful reservation
     * This should be called by whoever observes the success state
     * to ensure immediate UI update
     */
    fun handleSuccessfulReservation(cartViewModel: CartViewModel, reservationCode: String) {
        // Clear the cart first - this immediately updates UI state
        cartViewModel.clearCart()
        
        // Set success message with reservation code
        _success.value = "Đặt bàn thành công! Mã đặt bàn: $reservationCode"
    }
    
    private fun handleError(e: Exception) {
        val errorMsg = when (e) {
            is ConnectException, is UnknownHostException -> 
                "Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng."
            is SocketTimeoutException -> 
                "Kết nối tới máy chủ quá lâu. Vui lòng thử lại sau."
            is retrofit2.HttpException -> {
                val code = e.code()
                when (code) {
                    400 -> "Yêu cầu không hợp lệ. Vui lòng kiểm tra lại thông tin."
                    401 -> "Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại."
                    403 -> "Bạn không có quyền thực hiện thao tác này."
                    404 -> "Không tìm thấy thông tin yêu cầu."
                    else -> "Lỗi kết nối máy chủ (${code}). Vui lòng thử lại sau."
                }
            }
            else -> "Đã xảy ra lỗi: ${e.message}"
        }
        _error.value = errorMsg
        android.util.Log.e("ReservationViewModel", "Error details: ${e.message}", e)
    }
} 
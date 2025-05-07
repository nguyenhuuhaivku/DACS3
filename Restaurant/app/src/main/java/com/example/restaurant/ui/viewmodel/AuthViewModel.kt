package com.example.restaurant.ui.viewmodel

import android.app.Application
import android.util.Log
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.asLiveData
import androidx.lifecycle.viewModelScope
import com.example.restaurant.data.model.User
import com.example.restaurant.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.catch
import kotlinx.coroutines.launch

class AuthViewModel(application: Application) : AndroidViewModel(application) {
    private val TAG = "AuthViewModel"
    // Make repository public so MainActivity can access it directly
    val repository = AuthRepository(application)
    
    // Convert StateFlow to LiveData for UI updates
    val currentUser: LiveData<User?> = repository.currentUserFlow.asLiveData()
    val isLoggedIn: LiveData<Boolean> = repository.authStateFlow.asLiveData()
    
    // Add direct control of authentication state for immediate UI updates
    private val _directIsLoggedIn = MutableLiveData<Boolean>()
    private val _directCurrentUser = MutableLiveData<User?>()
    
    // Loading state
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    // Error messages
    private val _errorMessage = MutableLiveData<String?>()
    val errorMessage: LiveData<String?> = _errorMessage
    
    // Success messages
    private val _successMessage = MutableLiveData<String?>()
    val successMessage: LiveData<String?> = _successMessage
    
    init {
        // If logged in, refresh user data from server
        if (repository.isLoggedIn()) {
            _directIsLoggedIn.value = true
            _directCurrentUser.value = repository.getCurrentUser()
            refreshUserData()
        }
    }
    
    // Login
    fun login(email: String, password: String) {
        _isLoading.value = true
        _errorMessage.value = null
        _successMessage.value = null
        
        viewModelScope.launch {
            repository.login(email, password)
                .onSuccess { user ->
                    Log.d(TAG, "Login successful: ${user.name}")
                    // Update direct state immediately
                    _directIsLoggedIn.value = true
                    _directCurrentUser.value = user
                    
                    _successMessage.value = "Welcome back, ${user.name}!"
                    
                    // Then refresh data from repository
                    refreshUserData()
                }
                .onFailure { exception ->
                    Log.e(TAG, "Login failed: ${exception.message}")
                    _errorMessage.value = exception.message ?: "Unknown error occurred"
                    
                    // Ensure we're in logged out state
                    _directIsLoggedIn.value = false
                    _directCurrentUser.value = null
                }

            _isLoading.value = false
        }
    }
    
    // Register
    fun register(name: String, email: String, password: String, passwordConfirmation: String) {
        _isLoading.value = true
        _errorMessage.value = null
        _successMessage.value = null
        
        viewModelScope.launch {
            repository.register(name, email, password, passwordConfirmation)
                .onSuccess { user ->
                    Log.d(TAG, "Registration successful: ${user.name}")
                    // Update direct state immediately
                    _directIsLoggedIn.value = true
                    _directCurrentUser.value = user
                    
                    _successMessage.value = "Welcome, ${user.name}!"
                    
                    // Then refresh data from repository
                    refreshUserData()
                }
                .onFailure { exception ->
                    Log.e(TAG, "Registration failed: ${exception.message}")
                    _errorMessage.value = exception.message ?: "Unknown error occurred"
                    
                    // Ensure we're in logged out state
                    _directIsLoggedIn.value = false
                    _directCurrentUser.value = null
                }

            _isLoading.value = false
        }
    }
    
    // Logout
    fun logout() {
        _isLoading.value = true
        _errorMessage.value = null
        _successMessage.value = null
        
        // Immediately update direct state for UI
        _directIsLoggedIn.value = false
        _directCurrentUser.value = null
        
        viewModelScope.launch {
            repository.logout()
                .onSuccess {
                    Log.d(TAG, "Logout successful")
                    _successMessage.value = "You have been logged out"
                }
                .onFailure { exception ->
                    Log.e(TAG, "Logout failed: ${exception.message}")
                    _errorMessage.value = exception.message ?: "Unknown error occurred"
                }

            _isLoading.value = false
        }
    }
    
    // Method to refresh user data from server
    fun refreshUserData() {
        if (repository.isLoggedIn()) {
            _isLoading.value = true
            // Update direct state immediately based on repository cached data
            _directIsLoggedIn.value = true
            _directCurrentUser.value = repository.getCurrentUser()
            
            viewModelScope.launch {
                repository.getUserInfo()
                    .onSuccess { user ->
                        // Re-check and update direct state after fetch
                        _directIsLoggedIn.value = true
                        _directCurrentUser.value = user
                    }
                    .onFailure { exception ->
                        Log.e(TAG, "Failed to refresh user data: ${exception.message}")
                    }
                _isLoading.value = false
            }
        } else {
            // Clear direct state if not logged in
            _directIsLoggedIn.value = false
            _directCurrentUser.value = null
        }
    }
    
    // Clear error message
    fun clearErrorMessage() {
        _errorMessage.value = null
    }
    
    // Clear success message
    fun clearSuccessMessage() {
        _successMessage.value = null
    }
} 
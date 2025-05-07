package com.example.restaurant.data.repository

import android.content.Context
import android.content.SharedPreferences
import android.util.Log
import com.example.restaurant.data.api.RetrofitClient
import com.example.restaurant.data.model.LoginRequest
import com.example.restaurant.data.model.RegisterRequest
import com.example.restaurant.data.model.User
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.withContext

class AuthRepository(context: Context) {
    private val TAG = "AuthRepository"
    
    private val sharedPreferences: SharedPreferences = context.getSharedPreferences(
        "auth_prefs", Context.MODE_PRIVATE
    )
    
    // StateFlows to observe auth changes
    private val _authStateFlow = MutableStateFlow<Boolean>(false)
    val authStateFlow: StateFlow<Boolean> = _authStateFlow
    
    private val _currentUserFlow = MutableStateFlow<User?>(null)
    val currentUserFlow: StateFlow<User?> = _currentUserFlow
    
    companion object {
        private const val KEY_AUTH_TOKEN = "auth_token"
        private const val KEY_USER_ID = "user_id"
        private const val KEY_USER_NAME = "user_name"
        private const val KEY_USER_EMAIL = "user_email"
        private const val KEY_USER_ROLE = "user_role"
    }
    
    init {
        // Initialize state flows with current auth data
        _authStateFlow.value = !getAuthToken().isNullOrEmpty()
        _currentUserFlow.value = getCurrentUser()
    }
    
    suspend fun login(email: String, password: String): Result<User> {
        return withContext(Dispatchers.IO) {
            try {
                val loginRequest = LoginRequest(email, password)
                val response = RetrofitClient.authApiService.login(loginRequest)
                
                if (response.isSuccessful) {
                    val responseBody = response.body()
                    
                    if (responseBody?.success == true) {
                        val authResponse = responseBody.data
                        val user = authResponse?.user
                        val token = "${authResponse?.tokenType} ${authResponse?.accessToken}"
                        
                        if (user != null) {
                            // Save auth details
                            saveAuthDetails(token, user)
                            
                            // Update state flows
                            _authStateFlow.value = true
                            _currentUserFlow.value = user
                            
                            Log.d(TAG, "Login successful: ${user.name}")
                            Result.success(user)
                        } else {
                            Log.e(TAG, "Login failed: No user data in response")
                            Result.failure(Exception("No user data in response"))
                        }
                    } else {
                        Log.e(TAG, "Login failed: ${responseBody?.message}")
                        Result.failure(Exception(responseBody?.message ?: "Unknown error"))
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e(TAG, "Login failed: $errorBody")
                    Result.failure(Exception(errorBody ?: "Request failed with code ${response.code()}"))
                }
            } catch (e: Exception) {
                Log.e(TAG, "Login exception: ${e.message}", e)
                Result.failure(e)
            }
        }
    }
    
    suspend fun register(name: String, email: String, password: String, passwordConfirmation: String): Result<User> {
        return withContext(Dispatchers.IO) {
            try {
                val registerRequest = RegisterRequest(name, email, password, passwordConfirmation)
                val response = RetrofitClient.authApiService.register(registerRequest)
                
                if (response.isSuccessful) {
                    val responseBody = response.body()
                    
                    if (responseBody?.success == true) {
                        val authResponse = responseBody.data
                        val user = authResponse?.user
                        val token = "${authResponse?.tokenType} ${authResponse?.accessToken}"
                        
                        if (user != null) {
                            // Save auth details
                            saveAuthDetails(token, user)
                            
                            // Update state flows
                            _authStateFlow.value = true
                            _currentUserFlow.value = user
                            
                            Log.d(TAG, "Registration successful: ${user.name}")
                            Result.success(user)
                        } else {
                            Log.e(TAG, "Registration failed: No user data in response")
                            Result.failure(Exception("No user data in response"))
                        }
                    } else {
                        Log.e(TAG, "Registration failed: ${responseBody?.message}")
                        Result.failure(Exception(responseBody?.message ?: "Unknown error"))
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e(TAG, "Registration failed: $errorBody")
                    Result.failure(Exception(errorBody ?: "Request failed with code ${response.code()}"))
                }
            } catch (e: Exception) {
                Log.e(TAG, "Registration exception: ${e.message}", e)
                Result.failure(e)
            }
        }
    }
    
    suspend fun logout(): Result<Unit> {
        return withContext(Dispatchers.IO) {
            try {
                val token = getAuthToken()
                
                if (token.isNullOrEmpty()) {
                    clearAuthDetails()
                    return@withContext Result.success(Unit)
                }
                
                val response = RetrofitClient.authApiService.logout(token)
                
                if (response.isSuccessful) {
                    clearAuthDetails()
                    
                    // Update state flows
                    _authStateFlow.value = false
                    _currentUserFlow.value = null
                    
                    Log.d(TAG, "Logout successful")
                    Result.success(Unit)
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e(TAG, "Logout failed: $errorBody")
                    // Still clear local auth data even if the API call fails
                    clearAuthDetails()
                    
                    // Update state flows
                    _authStateFlow.value = false
                    _currentUserFlow.value = null
                    
                    Result.success(Unit)
                }
            } catch (e: Exception) {
                Log.e(TAG, "Logout exception: ${e.message}", e)
                // Still clear local auth data even if the API call fails
                clearAuthDetails()
                
                // Update state flows
                _authStateFlow.value = false
                _currentUserFlow.value = null
                
                Result.success(Unit)
            }
        }
    }
    
    suspend fun getUserInfo(): Result<User> {
        return withContext(Dispatchers.IO) {
            try {
                val token = getAuthToken()
                
                if (token.isNullOrEmpty()) {
                    return@withContext Result.failure(Exception("No auth token available"))
                }
                
                val response = RetrofitClient.authApiService.getUserInfo(token)
                
                if (response.isSuccessful) {
                    val responseBody = response.body()
                    
                    if (responseBody?.success == true) {
                        val userData = responseBody.data
                        val user = userData?.user
                        
                        if (user != null) {
                            // Update locally stored user details
                            updateUserDetails(user)
                            
                            // Update current user flow
                            _currentUserFlow.value = user
                            
                            Log.d(TAG, "Got user info successfully: ${user.name}")
                            Result.success(user)
                        } else {
                            Log.e(TAG, "Failed to get user info: No user data in response")
                            Result.failure(Exception("No user data in response"))
                        }
                    } else {
                        Log.e(TAG, "Failed to get user info: ${responseBody?.message}")
                        Result.failure(Exception(responseBody?.message ?: "Unknown error"))
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e(TAG, "Failed to get user info: $errorBody")
                    Result.failure(Exception(errorBody ?: "Request failed with code ${response.code()}"))
                }
            } catch (e: Exception) {
                Log.e(TAG, "Get user info exception: ${e.message}", e)
                Result.failure(e)
            }
        }
    }
    
    fun isLoggedIn(): Boolean {
        return !getAuthToken().isNullOrEmpty()
    }
    
    fun getAuthToken(): String? {
        return sharedPreferences.getString(KEY_AUTH_TOKEN, null)
    }
    
    fun getCurrentUser(): User? {
        val id = sharedPreferences.getInt(KEY_USER_ID, -1)
        if (id == -1) return null
        
        val name = sharedPreferences.getString(KEY_USER_NAME, "") ?: ""
        val email = sharedPreferences.getString(KEY_USER_EMAIL, "") ?: ""
        val role = sharedPreferences.getString(KEY_USER_ROLE, "") ?: ""
        
        return User(id, name, email, role)
    }
    
    private fun saveAuthDetails(token: String, user: User) {
        sharedPreferences.edit().apply {
            putString(KEY_AUTH_TOKEN, token)
            putInt(KEY_USER_ID, user.id)
            putString(KEY_USER_NAME, user.name)
            putString(KEY_USER_EMAIL, user.email)
            putString(KEY_USER_ROLE, user.roles)
        }.apply()
    }
    
    private fun updateUserDetails(user: User) {
        sharedPreferences.edit().apply {
            putInt(KEY_USER_ID, user.id)
            putString(KEY_USER_NAME, user.name)
            putString(KEY_USER_EMAIL, user.email)
            putString(KEY_USER_ROLE, user.roles)
        }.apply()
    }
    
    private fun clearAuthDetails() {
        sharedPreferences.edit().clear().apply()
    }
} 
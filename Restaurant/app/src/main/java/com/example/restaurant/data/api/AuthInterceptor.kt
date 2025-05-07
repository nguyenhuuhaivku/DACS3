package com.example.restaurant.data.api

import android.content.Context
import android.util.Log
import com.example.restaurant.data.utils.UserManager
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.runBlocking
import okhttp3.Interceptor
import okhttp3.Response

/**
 * Interceptor that adds the auth token to requests if available
 */
class AuthInterceptor(private val context: Context) : Interceptor {
    private val TAG = "AuthInterceptor"
    private val userManager = UserManager(context)
    
    override fun intercept(chain: Interceptor.Chain): Response {
        val originalRequest = chain.request()
        
        // If the request already has an Authorization header, use it as-is
        if (originalRequest.header("Authorization") != null) {
            return chain.proceed(originalRequest)
        }
        
        // Try to get the token if it exists
        val token = runBlocking {
            try {
                userManager.tokenFlow.first()
            } catch (e: Exception) {
                Log.e(TAG, "Failed to get token", e)
                null
            }
        }
        
        // If there's no token, proceed with the original request
        if (token == null) {
            Log.d(TAG, "No authentication token available")
            return chain.proceed(originalRequest)
        }
        
        // Add the token to the request
        val authenticatedRequest = originalRequest.newBuilder()
            .header("Authorization", token)
            .build()
        
        Log.d(TAG, "Added auth token to request")
        return chain.proceed(authenticatedRequest)
    }
} 
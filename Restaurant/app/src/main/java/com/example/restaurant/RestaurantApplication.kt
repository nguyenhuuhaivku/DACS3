package com.example.restaurant

import android.app.Application
import android.util.Log
import com.example.restaurant.data.api.RetrofitClient

class RestaurantApplication : Application() {
    private val TAG = "RestaurantApplication"
    
    override fun onCreate() {
        super.onCreate()
        
        // Initialize RetrofitClient with application context
        try {
            RetrofitClient.initialize(applicationContext)
            Log.d(TAG, "RetrofitClient initialized successfully")
        } catch (e: Exception) {
            Log.e(TAG, "Failed to initialize RetrofitClient", e)
        }
    }
} 
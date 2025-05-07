package com.example.restaurant.data.api

import android.content.Context
import android.os.Build
import android.util.Log
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import okhttp3.Interceptor
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.ResponseBody.Companion.toResponseBody
import okhttp3.Response
import java.io.IOException

object RetrofitClient {
    private const val TAG = "RetrofitClient"
    
    // Base URL options:
    
    // IMPORTANT: Choose which BASE_URL to use based on your environment:
    
    // For emulator use 10.0.2.2 which maps to your host machine's localhost
    private const val EMULATOR_BASE_URL = "http://10.0.2.2:8000/api/" 
    
    // For physical device use your actual IP address on your network
    private const val DEVICE_BASE_URL = "http://192.168.0.105:8000/api/" // Update this to your WiFi IP address
    
    // Set this to true when testing on a physical device, false for emulator
    private const val USE_PHYSICAL_DEVICE = false // Default to emulator for development
    
    // The active base URL based on the setting above
    private val BASE_URL = if (USE_PHYSICAL_DEVICE) DEVICE_BASE_URL else EMULATOR_BASE_URL
    
    // Getter method to expose base URL
    fun getBaseUrl(): String {
        val baseServerUrl = BASE_URL.replace("/api/", "")
        Log.d(TAG, "Base server URL: $baseServerUrl")
        return baseServerUrl
    }
    
    init {
        Log.d(TAG, "Khởi tạo RetrofitClient với BASE_URL: $BASE_URL")
    }
    
    private lateinit var okHttpClient: OkHttpClient
    private lateinit var retrofit: Retrofit
    private lateinit var apiServiceInstance: ApiService
    
    fun initialize(context: Context) {
        // Create OkHttpClient with logging
        okHttpClient = OkHttpClient.Builder()
            .addInterceptor(HttpLoggingInterceptor().apply {
                level = HttpLoggingInterceptor.Level.BODY
            })
            .addInterceptor(AuthInterceptor(context))
            .addInterceptor(object : Interceptor {
                override fun intercept(chain: Interceptor.Chain): Response {
                    val originalRequest = chain.request()
                    val requestUrl = originalRequest.url.toString()
                    Log.d(TAG, "Đang gửi request tới: $requestUrl")
                    Log.d(TAG, "Headers: ${originalRequest.headers}")
                    if (originalRequest.body != null) {
                        Log.d(TAG, "Body: ${originalRequest.body}")
                    }
                    
                    val request = originalRequest.newBuilder()
                        .addHeader("Accept", "application/json")
                        .build()
                    
                    try {
                        val response = chain.proceed(request)
                        val responseCode = response.code
                        
                        if (responseCode >= 400) {
                            val responseBody = response.body?.string() ?: ""
                            Log.e(TAG, "API error: HTTP $responseCode cho request $requestUrl")
                            Log.e(TAG, "Response body: $responseBody")
                            
                            // Recreate response body since we've consumed it
                            val contentType = response.body?.contentType()
                            val body = responseBody.toResponseBody(contentType)
                            
                            return response.newBuilder()
                                .body(body)
                                .build()
                        } else {
                            Log.d(TAG, "API success: HTTP $responseCode cho request $requestUrl")
                            return response
                        }
                    } catch (e: IOException) {
                        Log.e(TAG, "Network error: ${e.message} cho request $requestUrl", e)
                        throw e
                    } catch (e: Exception) {
                        Log.e(TAG, "API failure: ${e.message} cho request $requestUrl", e)
                        throw e
                    }
                }
            })
            .connectTimeout(30, TimeUnit.SECONDS) // Longer timeout for slower networks
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)
            .build()
    
        // Create Retrofit instance
        retrofit = Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    
        // Create API service
        apiServiceInstance = retrofit.create(ApiService::class.java)
    }
    
    // Get the API service, initializing if needed
    val apiService: ApiService
        get() {
            if (!::apiServiceInstance.isInitialized) {
                throw IllegalStateException("RetrofitClient must be initialized with context before use")
            }
            return apiServiceInstance
        }
    
    val authApiService: AuthApiService by lazy {
        retrofit.create(AuthApiService::class.java)
    }

} 
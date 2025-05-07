package com.example.restaurant.data.api

import com.example.restaurant.data.model.ApiResponse
import com.example.restaurant.data.model.AuthResponse
import com.example.restaurant.data.model.LoginRequest
import com.example.restaurant.data.model.RegisterRequest
import com.example.restaurant.data.model.UserData
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.POST

interface AuthApiService {
    
    @POST("login")
    suspend fun login(@Body loginRequest: LoginRequest): Response<ApiResponse<AuthResponse>>
    
    @POST("register")
    suspend fun register(@Body registerRequest: RegisterRequest): Response<ApiResponse<AuthResponse>>
    
    @GET("user")
    suspend fun getUserInfo(@Header("Authorization") token: String): Response<ApiResponse<UserData>>
    
    @POST("logout")
    suspend fun logout(@Header("Authorization") token: String): Response<ApiResponse<Unit>>
} 
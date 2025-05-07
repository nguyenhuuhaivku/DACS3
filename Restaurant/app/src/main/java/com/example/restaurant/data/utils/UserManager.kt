package com.example.restaurant.data.utils

import android.content.Context
import android.content.SharedPreferences
import android.util.Log
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import com.example.restaurant.data.model.User
import com.google.gson.Gson
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "user_preferences")

class UserManager(private val context: Context) {
    private val TAG = "UserManager"
    
    companion object {
        private val USER_TOKEN_KEY = stringPreferencesKey("user_token")
        private val USER_DATA_KEY = stringPreferencesKey("user_data")
    }
    
    // Lưu thông tin token
    suspend fun saveToken(token: String) {
        context.dataStore.edit { preferences ->
            preferences[USER_TOKEN_KEY] = token
            Log.d(TAG, "Token đã được lưu")
        }
    }
    
    // Lấy token
    val tokenFlow: Flow<String?> = context.dataStore.data
        .map { preferences ->
            preferences[USER_TOKEN_KEY]
        }
    
    // Lưu thông tin user
    suspend fun saveUser(user: User) {
        val userJson = Gson().toJson(user)
        context.dataStore.edit { preferences ->
            preferences[USER_DATA_KEY] = userJson
            Log.d(TAG, "Thông tin user đã được lưu: ${user.name}")
        }
    }
    
    // Lấy thông tin user
    val userFlow: Flow<User?> = context.dataStore.data
        .map { preferences ->
            val userJson = preferences[USER_DATA_KEY]
            if (userJson != null) {
                try {
                    Gson().fromJson(userJson, User::class.java)
                } catch (e: Exception) {
                    Log.e(TAG, "Lỗi khi parse user data: ${e.message}")
                    null
                }
            } else {
                null
            }
        }
    
    // Xóa thông tin khi đăng xuất
    suspend fun clearUserData() {
        context.dataStore.edit { preferences ->
            preferences.remove(USER_TOKEN_KEY)
            preferences.remove(USER_DATA_KEY)
            Log.d(TAG, "Đã xóa thông tin đăng nhập")
        }
    }
    
    // Kiểm tra user đã đăng nhập chưa
    val isLoggedIn: Flow<Boolean> = tokenFlow.map { token ->
        !token.isNullOrEmpty()
    }
} 
package com.example.restaurant.data.repository

import android.util.Log
import com.example.restaurant.data.api.RetrofitClient
import com.example.restaurant.data.model.MenuCategory
import com.example.restaurant.data.model.MenuItem
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException

class MenuRepository {
    private val TAG = "MenuRepository"
    private val apiService = RetrofitClient.apiService
    
    suspend fun getCategories(): Result<List<MenuCategory>> {
        return try {
            withContext(Dispatchers.IO) {
                val response = apiService.getMenuCategories()
                if (response.success && response.data != null) {
                    Result.success(response.data)
                } else {
                    Result.failure(Exception(response.message ?: "Unknown error"))
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting categories", e)
            Result.failure(handleApiError(e))
        }
    }
    
    suspend fun getAllMenuItems(): Result<List<MenuItem>> {
        return try {
            withContext(Dispatchers.IO) {
                val response = apiService.getAllMenuItems()
                if (response.success && response.data != null) {
                    // Process each menu item to fix image URLs
                    val baseUrl = RetrofitClient.getBaseUrl()
                    val processedItems = response.data.map { item ->
                        // Create a complete image URL by prepending the base URL if it's a relative path
                        val completeImageUrl = item.imageUrl?.let { url ->
                            if (url.startsWith("http")) url else "$baseUrl/$url"
                        }
                        
                        Log.d(TAG, "MenuItem: ${item.name}, Original ImageURL: ${item.imageUrl}, Complete URL: $completeImageUrl")
                        
                        // Create a new MenuItem with the updated imageUrl
                        item.copy(imageUrl = completeImageUrl)
                    }
                    
                    Result.success(processedItems)
                } else {
                    Result.failure(Exception(response.message ?: "Unknown error"))
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting all menu items", e)
            Result.failure(handleApiError(e))
        }
    }
    
    suspend fun getMenuItem(itemId: Int): Result<MenuItem> {
        return try {
            withContext(Dispatchers.IO) {
                val response = apiService.getMenuItem(itemId)
                if (response.success && response.data != null) {
                    // Fix the image URL by prepending the base URL if it's a relative path
                    val baseUrl = RetrofitClient.getBaseUrl()
                    val item = response.data
                    val completeImageUrl = item.imageUrl?.let { url ->
                        if (url.startsWith("http")) url else "$baseUrl/$url"
                    }
                    
                    Log.d(TAG, "Single MenuItem: ${item.name}, Original ImageURL: ${item.imageUrl}, Complete URL: $completeImageUrl")
                    
                    // Return a new MenuItem with the updated imageUrl
                    Result.success(item.copy(imageUrl = completeImageUrl))
                } else {
                    Result.failure(Exception(response.message ?: "Unknown error"))
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting menu item: $itemId", e)
            Result.failure(handleApiError(e))
        }
    }
    
    suspend fun getMenuItemsByCategory(categoryId: Int): Result<Pair<MenuCategory, List<MenuItem>>> {
        return try {
            withContext(Dispatchers.IO) {
                val response = apiService.getMenuItemsByCategory(categoryId)
                if (response.success && response.data != null) {
                    val categoryData = response.data["category"] as? Map<String, Any>
                    val itemsData = response.data["items"] as? List<Map<String, Any>>
                    
                    if (categoryData != null && itemsData != null) {
                        // This is a simplified approach - in a real app you would use proper deserialization
                        // Here we're assuming the structure matches our data classes
                        val category = MenuCategory(
                            id = (categoryData["CategoryID"] as Number).toInt(),
                            name = categoryData["CategoryName"] as String,
                            description = categoryData["Description"] as? String,
                            createdAt = categoryData["CreatedAt"] as? String,
                            updatedAt = categoryData["UpdatedAt"] as? String
                        )
                        
                        val baseUrl = RetrofitClient.getBaseUrl()
                        val items = itemsData.map { item ->
                            // Get the original image URL
                            val originalImageUrl = item["ImageURL"] as? String
                            
                            // Create a complete image URL by prepending the base URL if it's a relative path
                            val completeImageUrl = originalImageUrl?.let { url ->
                                if (url.startsWith("http")) url else "$baseUrl/$url"
                            }
                            
                            Log.d(TAG, "Category Item: ${item["ItemName"]}, Original ImageURL: $originalImageUrl, Complete URL: $completeImageUrl")
                            
                            MenuItem(
                                id = (item["ItemID"] as Number).toInt(),
                                categoryId = (item["CategoryID"] as Number).toInt(),
                                name = item["ItemName"] as String,
                                price = (item["Price"] as Number).toDouble(),
                                status = item["status"] as String,
                                description = item["Description"] as? String,
                                available = (item["Available"] as Number).toInt(),
                                imageUrl = completeImageUrl,
                                createdAt = item["CreatedAt"] as? String,
                                updatedAt = item["UpdatedAt"] as? String
                            )
                        }
                        
                        Result.success(Pair(category, items))
                    } else {
                        Result.failure(Exception("Invalid response format"))
                    }
                } else {
                    Result.failure(Exception(response.message ?: "Unknown error"))
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error getting menu items by category: $categoryId", e)
            Result.failure(handleApiError(e))
        }
    }
    
    private fun handleApiError(e: Exception): Exception {
        val errorMessage = when (e) {
            is UnknownHostException -> "Không thể kết nối đến server. Vui lòng kiểm tra địa chỉ IP và đảm bảo server đang chạy."
            is ConnectException -> "Kết nối bị từ chối. Đảm bảo server Laravel đang chạy và địa chỉ IP chính xác."
            is SocketTimeoutException -> "Kết nối tới server quá thời gian chờ. Vui lòng thử lại sau."
            else -> "Lỗi kết nối: ${e.message}"
        }
        
        // Log detailed error information
        Log.e(TAG, "API Error: $errorMessage", e)
        return Exception(errorMessage)
    }
} 
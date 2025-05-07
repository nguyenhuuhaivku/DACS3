package com.example.restaurant.ui.viewmodel

import android.util.Log
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.restaurant.data.model.MenuCategory
import com.example.restaurant.data.model.MenuItem
import com.example.restaurant.data.repository.MenuRepository
import kotlinx.coroutines.launch
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException

class MenuViewModel : ViewModel() {
    private val TAG = "MenuViewModel"
    private val repository = MenuRepository()
    
    // Menu Categories
    private val _categories = MutableLiveData<List<MenuCategory>>()
    val categories: LiveData<List<MenuCategory>> = _categories
    
    // Menu Items
    private val _menuItems = MutableLiveData<List<MenuItem>>()
    val menuItems: LiveData<List<MenuItem>> = _menuItems
    
    // Selected Category
    private val _selectedCategory = MutableLiveData<Int?>(null)
    val selectedCategory: LiveData<Int?> = _selectedCategory
    
    // Filtered Items for the selected category
    private val _filteredItems = MutableLiveData<List<MenuItem>>()
    val filteredItems: LiveData<List<MenuItem>> = _filteredItems
    
    // Search query
    private val _searchQuery = MutableLiveData<String>("")
    val searchQuery: LiveData<String> = _searchQuery
    
    // Loading state
    private val _isLoading = MutableLiveData<Boolean>(false)
    val isLoading: LiveData<Boolean> = _isLoading
    
    // Error state
    private val _error = MutableLiveData<String?>(null)
    val error: LiveData<String?> = _error
    
    init {
        loadCategories()
        loadAllMenuItems()
    }
    
    fun loadCategories() {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            
            try {
                Log.d(TAG, "Bắt đầu tải danh mục...")
                repository.getCategories().fold(
                    onSuccess = { 
                        _categories.value = it
                        _isLoading.value = false
                        Log.d(TAG, "Đã tải ${it.size} danh mục thành công")
                    },
                    onFailure = { 
                        val errorMessage = handleError(it)
                        Log.e(TAG, "Lỗi tải danh mục: $errorMessage", it)
                        _error.value = errorMessage
                        _isLoading.value = false
                    }
                )
            } catch (e: Exception) {
                val errorMessage = handleError(e)
                Log.e(TAG, "Exception khi tải danh mục: $errorMessage", e)
                _error.value = errorMessage
                _isLoading.value = false
            }
        }
    }
    
    fun loadAllMenuItems() {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            
            try {
                Log.d(TAG, "Bắt đầu tải tất cả món ăn...")
                repository.getAllMenuItems().fold(
                    onSuccess = { items -> 
                        // Log image URLs for debugging
                        items.forEach { item ->
                            Log.d(TAG, "Món ăn: ${item.name}, URL hình ảnh: ${item.imageUrl}")
                        }
                        
                        _menuItems.value = items
                        // Apply any filters (search or category)
                        applyFilters()
                        _isLoading.value = false
                        Log.d(TAG, "Đã tải ${items.size} món ăn thành công")
                    },
                    onFailure = { 
                        val errorMessage = handleError(it)
                        Log.e(TAG, "Lỗi tải món ăn: $errorMessage", it)
                        _error.value = errorMessage
                        _isLoading.value = false
                    }
                )
            } catch (e: Exception) {
                val errorMessage = handleError(e)
                Log.e(TAG, "Exception khi tải món ăn: $errorMessage", e)
                _error.value = errorMessage
                _isLoading.value = false
            }
        }
    }
    
    private fun handleError(e: Throwable): String {
        return when (e) {
            is UnknownHostException -> "Không thể kết nối đến server. Vui lòng kiểm tra:\n1. Server Laravel đang chạy\n2. Địa chỉ IP trong RetrofitClient.kt phù hợp với thiết bị của bạn (emulator hoặc thiết bị thực)"
            is ConnectException -> "Kết nối bị từ chối. Đảm bảo:\n1. Server Laravel đang chạy\n2. Địa chỉ IP trong RetrofitClient.kt chính xác\n3. Cổng 8000 đang mở"
            is SocketTimeoutException -> "Kết nối tới server quá thời gian chờ. Vui lòng thử lại sau hoặc kiểm tra kết nối mạng."
            else -> "Lỗi: ${e.message ?: "Không xác định"}"
        }
    }
    
    fun selectCategory(categoryId: Int?) {
        _selectedCategory.value = categoryId
        applyFilters()
    }
    
    // Set search query and apply filters
    fun setSearchQuery(query: String) {
        _searchQuery.value = query
        applyFilters()
    }
    
    // Apply both category filter and search filter together
    private fun applyFilters() {
        _menuItems.value?.let { allItems ->
            var filteredList = allItems
            
            // Apply category filter if selected
            val categoryId = _selectedCategory.value
            if (categoryId != null) {
                filteredList = filteredList.filter { it.categoryId == categoryId }
            }
            
            // Apply search filter if query exists
            val query = _searchQuery.value
            if (!query.isNullOrBlank()) {
                val searchTermLower = query.lowercase()
                filteredList = filteredList.filter { 
                    it.name.lowercase().contains(searchTermLower) || 
                    (it.description?.lowercase()?.contains(searchTermLower) ?: false)
                }
            }
            
            _filteredItems.value = filteredList
        }
    }
    
    private fun loadItemsByCategory(categoryId: Int) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            
            repository.getMenuItemsByCategory(categoryId).fold(
                onSuccess = { (_, items) -> 
                    // Log image URLs for the category items
                    items.forEach { item ->
                        Log.d(TAG, "Món ăn theo danh mục: ${item.name}, URL hình ảnh: ${item.imageUrl}")
                    }
                    
                    // Store these items and also apply any search filtering
                    _menuItems.value = items
                    applyFilters()
                    _isLoading.value = false 
                },
                onFailure = { 
                    val errorMessage = handleError(it)
                    _error.value = errorMessage
                    _isLoading.value = false 
                }
            )
        }
    }
    
    fun clearError() {
        _error.value = null
    }
    
    // Function to clear search query
    fun clearSearchQuery() {
        _searchQuery.value = ""
        applyFilters()
    }
} 
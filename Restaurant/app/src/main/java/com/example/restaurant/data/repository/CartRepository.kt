package com.example.restaurant.data.repository

import android.content.Context
import android.content.SharedPreferences
import android.util.Log
import com.example.restaurant.R
import com.example.restaurant.data.api.RetrofitClient
import com.example.restaurant.data.model.MenuItem
import com.example.restaurant.ui.viewmodel.CartItem
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken

class CartRepository(context: Context) {
    private val TAG = "CartRepository"
    
    private val sharedPreferences: SharedPreferences = context.getSharedPreferences(
        "cart_prefs", Context.MODE_PRIVATE
    )
    
    private val gson = Gson()
    
    companion object {
        private const val KEY_CART_ITEMS_PREFIX = "cart_items_user_"
        private const val KEY_ANONYMOUS_CART = "cart_items_anonymous"
    }
    
    /**
     * Get cart items for a specific user or anonymous cart
     */
    fun getCartItems(userId: Int? = null): List<CartItem> {
        val key = getCartKey(userId)
        val cartItemsJson = sharedPreferences.getString(key, null) ?: return emptyList()
        
        return try {
            val type = object : TypeToken<List<CartItem>>() {}.type
            gson.fromJson(cartItemsJson, type)
        } catch (e: Exception) {
            Log.e(TAG, "Error retrieving cart items: ${e.message}", e)
            emptyList()
        }
    }
    
    /**
     * Save cart items for a specific user or anonymous cart
     */
    fun saveCartItems(cartItems: List<CartItem>, userId: Int? = null) {
        try {
            val key = getCartKey(userId)
            val cartItemsJson = gson.toJson(cartItems)
            sharedPreferences.edit().putString(key, cartItemsJson).apply()
        } catch (e: Exception) {
            Log.e(TAG, "Error saving cart items: ${e.message}", e)
        }
    }
    
    /**
     * Add an item to the cart for a specific user
     */
    fun addToCart(menuItem: MenuItem, userId: Int? = null): List<CartItem> {
        val currentItems = getCartItems(userId).toMutableList()
        val existingItemIndex = currentItems.indexOfFirst { it.id == menuItem.id }
        
        if (existingItemIndex != -1) {
            // Item already in cart, increment quantity
            val existingItem = currentItems[existingItemIndex]
            currentItems[existingItemIndex] = existingItem.copy(quantity = existingItem.quantity + 1)
        } else {
            // New item, add to cart with complete image URL
            val fullImageUrl = menuItem.getCompleteImageUrl()
            
            Log.d(TAG, "Full image URL: $fullImageUrl (original: ${menuItem.imageUrl})")
            
            val newItem = CartItem(
                id = menuItem.id,
                name = menuItem.name,
                price = menuItem.price,
                imageUrl = fullImageUrl,
                imageRes = R.drawable.placeholder_food,
                quantity = 1
            )
            currentItems.add(newItem)
        }
        
        // Make sure to actually save the items
        saveCartItems(currentItems, userId)
        return currentItems
    }
    
    /**
     * Update item quantity for a specific user
     */
    fun updateItemQuantity(itemId: Int, newQuantity: Int, userId: Int? = null): List<CartItem> {
        if (newQuantity <= 0) {
            return removeItem(itemId, userId)
        }
        
        val currentItems = getCartItems(userId).toMutableList()
        val updatedItems = currentItems.map { 
            if (it.id == itemId) it.copy(quantity = newQuantity) else it 
        }
        
        saveCartItems(updatedItems, userId)
        return updatedItems
    }
    
    /**
     * Remove an item from the cart for a specific user
     */
    fun removeItem(itemId: Int, userId: Int? = null): List<CartItem> {
        val currentItems = getCartItems(userId).toMutableList()
        val updatedItems = currentItems.filter { it.id != itemId }
        
        saveCartItems(updatedItems, userId)
        return updatedItems
    }
    
    /**
     * Clear the cart for a specific user
     */
    fun clearCart(userId: Int? = null): List<CartItem> {
        saveCartItems(emptyList(), userId)
        return emptyList()
    }
    
    /**
     * Copy anonymous cart to user cart upon login
     */
    fun transferAnonymousCartToUser(userId: Int): List<CartItem> {
        val anonymousCart = getCartItems(null)
        
        // If anonymous cart is not empty, transfer it to the user cart
        if (anonymousCart.isNotEmpty()) {
            saveCartItems(anonymousCart, userId)
            
            // Clear anonymous cart after transfer
            clearCart(null)
        }
        
        return anonymousCart
    }
    
    /**
     * Get the appropriate key for storing cart items based on userId
     */
    private fun getCartKey(userId: Int?): String {
        return if (userId != null && userId > 0) {
            "$KEY_CART_ITEMS_PREFIX$userId"
        } else {
            KEY_ANONYMOUS_CART
        }
    }
} 
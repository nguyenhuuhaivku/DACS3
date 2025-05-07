<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Add an item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $id = $request->id;
        $quantity = $request->quantity;
        
        $menu = Menu::findOrFail($id);
        $cart = Session::get('cart', []);
        
        // If item already exists in cart, update quantity
        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            // Add new item to cart
            $cart[$id] = [
                'name' => $menu->name,
                'quantity' => $quantity,
                'price' => $menu->price,
                'image' => $menu->image,
                'special_instructions' => $request->special_instructions ?? '',
            ];
        }
        
        Session::put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => __('Item added to cart successfully!'),
            'cart_count' => $this->getCartCount(),
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $id = $request->id;
        $quantity = $request->quantity;
        
        $cart = Session::get('cart', []);
        
        if(isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            Session::put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => __('Cart updated successfully!'),
                'cart_count' => $this->getCartCount(),
                'item_price' => $cart[$id]['price'],
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => __('Item not found in cart!'),
        ], 404);
    }

    /**
     * Remove an item from cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->id;
        $cart = Session::get('cart', []);
        
        if(isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => __('Item removed from cart successfully!'),
                'cart_count' => $this->getCartCount(),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => __('Item not found in cart!'),
        ], 404);
    }

    /**
     * Clear cart.
     */
    public function clear()
    {
        Session::forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => __('Cart cleared successfully!'),
        ]);
    }

    /**
     * Get cart information.
     */
    public function info()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $taxRate = 0.1; // 10% tax
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax;
        
        return response()->json([
            'items' => $cart,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'count' => $this->getCartCount(),
        ]);
    }

    /**
     * Display cart view.
     */
    public function show()
    {
        return view('cart.index');
    }

    /**
     * Get total items count in cart.
     */
    private function getCartCount()
    {
        $cart = Session::get('cart', []);
        $count = 0;
        
        foreach($cart as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }
} 
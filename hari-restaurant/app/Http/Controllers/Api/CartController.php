<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get cart items for authenticated user
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            
            $cartItems = DB::table('cart')
                ->join('menuitem', 'cart.item_id', '=', 'menuitem.ItemID')
                ->where('cart.user_id', $userId)
                ->select(
                    'cart.id',
                    'cart.user_id',
                    'cart.item_id',
                    'cart.quantity',
                    'menuitem.ItemName as name',
                    'menuitem.Price as price',
                    'menuitem.ImageURL as image'
                )
                ->get();
            
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item->price * $item->quantity;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $cartItems,
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add an item to cart
     */
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:menuitem,ItemID',
                'quantity' => 'required|integer|min:1'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $userId = Auth::id();
            $itemId = $request->item_id;
            $quantity = $request->quantity;
            
            // Kiểm tra xem món ăn đã có trong giỏ hàng chưa
            $existingItem = DB::table('cart')
                ->where('user_id', $userId)
                ->where('item_id', $itemId)
                ->first();
            
            if ($existingItem) {
                // Nếu đã có, cập nhật số lượng
                DB::table('cart')
                    ->where('id', $existingItem->id)
                    ->update([
                        'quantity' => $existingItem->quantity + $quantity,
                        'updated_at' => now()
                    ]);
            } else {
                // Nếu chưa có, thêm mới
                DB::table('cart')->insert([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Trả về thông tin giỏ hàng mới
            return $this->index();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $userId = Auth::id();
            $quantity = $request->quantity;
            
            // Kiểm tra xem cart item có tồn tại không
            $cartItem = DB::table('cart')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy item trong giỏ hàng'
                ], 404);
            }
            
            // Cập nhật số lượng
            DB::table('cart')
                ->where('id', $id)
                ->update([
                    'quantity' => $quantity,
                    'updated_at' => now()
                ]);
            
            // Trả về thông tin giỏ hàng mới
            return $this->index();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        try {
            $userId = Auth::id();
            
            // Kiểm tra xem cart item có tồn tại không
            $cartItem = DB::table('cart')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy item trong giỏ hàng'
                ], 404);
            }
            
            // Xóa item khỏi giỏ hàng
            DB::table('cart')
                ->where('id', $id)
                ->delete();
            
            // Trả về thông tin giỏ hàng mới
            return $this->index();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
} 
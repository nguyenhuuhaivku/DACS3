<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class MenuController extends Controller
{
    /**
     * Get all menu categories
     */
    public function getCategories()
    {
        try {
            $categories = DB::table('menucategory')->get();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all menu items
     */
    public function getAllItems()
    {
        try {
            $items = DB::table('menuitem')->get()->map(function($item) {
                // Nếu ImageURL không bắt đầu bằng http:// hoặc https://, thêm base URL
                if ($item->ImageURL && !preg_match("~^(?:f|ht)tps?://~i", $item->ImageURL)) {
                    $item->ImageURL = URL::to('/' . $item->ImageURL);
                }
                return $item;
            });
            
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific menu item
     */
    public function getItem($id)
    {
        try {
            $item = DB::table('menuitem')->where('ItemID', $id)->first();
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy món ăn'
                ], 404);
            }
            
            // Xử lý URL hình ảnh
            if ($item->ImageURL && !preg_match("~^(?:f|ht)tps?://~i", $item->ImageURL)) {
                $item->ImageURL = URL::to('/' . $item->ImageURL);
            }
            
            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get menu items by category
     */
    public function getItemsByCategory($categoryId)
    {
        try {
            $category = DB::table('menucategory')->where('CategoryID', $categoryId)->first();
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy danh mục'
                ], 404);
            }
            
            $items = DB::table('menuitem')
                ->where('CategoryID', $categoryId)
                ->get()
                ->map(function($item) {
                    // Xử lý URL hình ảnh
                    if ($item->ImageURL && !preg_match("~^(?:f|ht)tps?://~i", $item->ImageURL)) {
                        $item->ImageURL = URL::to('/' . $item->ImageURL);
                    }
                    return $item;
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'items' => $items
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
} 
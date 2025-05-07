<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Models\user\MenuItem;
use App\Models\user\MenuCategory;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::with(['menuItems' => function ($query) {
            $query->orderBy('ItemID', 'desc');
        }])->get();
        $allItems = MenuItem::orderBy('CreatedAt', 'desc')->get();
        return view('user.menu', compact('categories', 'allItems'));
    }
    public function search(Request $request)
    {
        try {
            $menuItems = MenuItem::where('ItemName', 'LIKE', "%{$request->query}%")
                ->get();
            return response()->json([
                'success' => true,
                'data' => $menuItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm'
            ], 500);
        }
    }
}

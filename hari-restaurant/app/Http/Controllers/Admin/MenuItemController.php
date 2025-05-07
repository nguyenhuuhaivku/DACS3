<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\MenuCategory;
use App\Models\admin\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->orderBy('ItemID', 'desc')->get();
        return view('admin.menu.items.index', compact('items'));
    }

    public function create()
    {
        $categories = MenuCategory::all();
        $defaultStatus = MenuItem::STATUS_NORMAL;
        return view('admin.menu.items.create', compact('categories', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CategoryID' => 'required|exists:menucategory,CategoryID',
            'ItemName' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'status' => 'required|in:Món mới,Phổ biến,Đặc biệt,Bình thường',
            'Description' => 'nullable|string',
            'Image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'Available' => 'required|boolean',
        ]);

        if ($request->hasFile('Image')) {
            $directory = public_path('images/');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $fileName = time() . '_' . $request->file('Image')->getClientOriginalName();
            $request->file('Image')->move($directory, $fileName);

            $validated['ImageURL'] = 'images/' . $fileName;
        }

        MenuItem::create($validated);

        return redirect()->route('admin.menu.items.index')->with('success', 'Món ăn đã được thêm thành công.');
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'CategoryID' => 'required|exists:menucategory,CategoryID',
            'ItemName' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'status' => 'required|in:Món mới,Phổ biến,Đặc biệt,Bình thường',
            'Description' => 'nullable|string',
            'Image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'Available' => 'required|boolean',
        ]);

        $item = MenuItem::findOrFail($id);

        if ($request->hasFile('Image')) {
            $directory = public_path('images/');

            if ($item->ImageURL && file_exists(public_path($item->ImageURL))) {
                unlink(public_path($item->ImageURL));
            }

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            // Lưu ảnh mới
            $fileName = time() . '_' . $request->file('Image')->getClientOriginalName();
            $request->file('Image')->move($directory, $fileName);

            $validated['ImageURL'] = 'images/' . $fileName;
        }

        $item->update($validated);

        return redirect()->route('admin.menu.items.index')->with('success', 'Món ăn đã được cập nhật.');
    }


    public function edit($id)
    {
        $item = MenuItem::findOrFail($id);
        $categories = MenuCategory::all();
        return view('admin.menu.items.edit', compact('item', 'categories'));
    }


    public function destroy($id)
    {
        $item = MenuItem::findOrFail($id);

        // Xóa ảnh nếu tồn tại
        if ($item->ImageURL && file_exists(public_path($item->ImageURL))) {
            unlink(public_path($item->ImageURL));
        }

        $item->delete();

        return redirect()->route('admin.menu.items.index')->with('success', 'Món ăn đã được xóa.');
    }
}

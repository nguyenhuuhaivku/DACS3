<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::all();
        return view('admin.menu.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.menu.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'CategoryName' => 'required|string|max:255',
            'Description' => 'nullable|string',
        ]);

        MenuCategory::create($request->all());
        return redirect()->route('admin.menu.categories.index')->with('success', 'Danh mục đã được thêm thành công.');
    }

    public function edit($id)
    {
        $category = MenuCategory::findOrFail($id);
        return view('admin.menu.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'CategoryName' => 'required|string|max:255',
            'Description' => 'nullable|string',
        ]);

        $category = MenuCategory::findOrFail($id);
        $category->update($request->all());
        return redirect()->route('admin.menu.categories.index')->with('success', 'Danh mục đã được cập nhật.');
    }

    public function destroy($id)
    {
        MenuCategory::destroy($id);
        return redirect()->route('admin.menu.categories.index')->with('success', 'Danh mục đã được xóa.');
    }
}

<?php

namespace App\Http\Controllers\Admin\table;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;


class TableController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách vị trí (để hiển thị danh mục)
        $locations = Table::select('Location')->distinct()->get();

        // Lọc bàn theo vị trí nếu có yêu cầu
        $selectedLocation = $request->input('location');
        $tables = Table::when($selectedLocation, function ($query) use ($selectedLocation) {
            return $query->where('Location', $selectedLocation);
        })
        ->withCount(['activeReservations' => function($query) {
            $query->where('Status', 'Đã xác nhận');
        }])
        ->get();

        return view('admin.tables.index', compact('tables', 'locations', 'selectedLocation'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TableNumber' => 'required|string|max:10|unique:table',
            'Seats' => 'required|integer|min:1',
            'Location' => 'required|in:Trong nhà,Ngoài sân,VIP',
            'Status' => 'required|in:Trống,Đang sử dụng,Bảo trì',
        ]);

        Table::create($request->all());

        return redirect()->route('admin.tables.index')->with('success', 'Bàn đã được thêm thành công!');
    }

    public function edit($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'TableNumber' => 'required|string|max:10|unique:table,TableNumber,' . $id . ',TableID',
            'Seats' => 'required|integer|min:1',
            'Location' => 'required|in:Trong nhà,Ngoài sân,VIP',
            'Status' => 'required|in:Trống,Đang sử dụng,Bảo trì',
        ]);

        $table->update([
            'TableNumber' => $request->TableNumber,
            'Seats' => $request->Seats,
            'Location' => $request->Location,
            'Status' => $request->Status,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Thông tin bàn đã được cập nhật!');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        
        if ($table->Status === 'Đang sử dụng') {
            return redirect()->route('admin.tables.index')
                ->with('error', 'Không thể xóa bàn đang được sử dụng!');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Bàn đã được xóa thành công!');
    }
}

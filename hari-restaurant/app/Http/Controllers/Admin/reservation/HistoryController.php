<?php


namespace App\Http\Controllers\admin\reservation;


use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Http\Controllers\Controller;


class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::whereIn('Status', ['Đã hoàn tất', 'Đã hủy']);


        // Lọc theo trạng thái
        if ($request->status) {
            $query->where('Status', $request->status);
        }


        // Lọc theo thời gian
        if ($request->period) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('ReservationDate', today());
                    break;
                case 'week':
                    $query->whereBetween('ReservationDate', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('ReservationDate', now()->month)
                        ->whereYear('ReservationDate', now()->year);
                    break;
                case 'year':
                    $query->whereYear('ReservationDate', now()->year);
                    break;
                case 'custom':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('ReservationDate', [$request->start_date, $request->end_date]);
                    }
                    break;
            }
        }


        $reservations = $query->orderBy('ReservationDate', 'desc')->paginate(10);


        return view('admin.reservations.history', compact('reservations'));
    }
}

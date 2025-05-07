<?php
































namespace App\Http\Controllers\Admin;
































use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
































class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');








        // Xác định khoảng thời gian và format
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                $groupByFormat = 'HOUR';
                $selectFormat = '%H:00';
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $groupByFormat = 'DAY';
                $selectFormat = '%d/%m';
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $groupByFormat = 'DAY';
                $selectFormat = '%d/%m';
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $groupByFormat = 'MONTH';
                $selectFormat = '%m/%Y';
                break;
            case 'custom':
                $startDate = Carbon::parse($customStart)->startOfDay();
                $endDate = Carbon::parse($customEnd)->endOfDay();
                $diffInDays = $startDate->diffInDays($endDate);
                if ($diffInDays <= 1) {
                    $groupByFormat = 'HOUR';
                    $selectFormat = '%H:00';
                } elseif ($diffInDays <= 31) {
                    $groupByFormat = 'DAY';
                    $selectFormat = '%d/%m';
                } else {
                    $groupByFormat = 'MONTH';
                    $selectFormat = '%m/%Y';
                }
                break;
            default:
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                $groupByFormat = 'HOUR';
                $selectFormat = '%H:00';
        }








        // Thống kê tổng quan
        $statistics = [
            'total_orders' => Reservation::whereBetween('CreatedAt', [$startDate, $endDate])
                ->whereIn('Status', ['Đã xác nhận', 'Đã hoàn tất'])
                ->count(),
            'total_revenue' => Payment::whereBetween('CreatedAt', [$startDate, $endDate])
                ->where('Status', 'Đã thanh toán')
                ->sum('Amount'),
            'total_customers' => User::where('roles', 'User')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'average_order_value' => Payment::where('Status', 'Đã thanh toán')
                ->whereBetween('CreatedAt', [$startDate, $endDate])
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('reservation')
                        ->whereColumn('payment.ReservationID', 'reservation.ReservationID')
                        ->whereIn('reservation.Status', ['Đã xác nhận', 'Đã hoàn tất']);
                })
                ->avg('Amount') ?? 0,
        ];
















        // Top 5 khách hàng
        $topCustomers = DB::table('users')
            ->select(
                'users.name',
                DB::raw('COUNT(DISTINCT r.ReservationID) as reservations_count'),
                DB::raw('COALESCE(SUM(CASE WHEN p.Status = "Đã thanh toán" THEN p.Amount ELSE 0 END), 0) as total_spent')
            )
            ->leftJoin('reservation as r', 'users.id', '=', 'r.UserID')
            ->leftJoin('payment as p', 'r.ReservationID', '=', 'p.ReservationID')
            ->where('users.roles', '=', 'User')
            ->whereIn('r.Status', ['Đã xác nhận', 'Đã hoàn tất'])
            ->whereBetween('r.CreatedAt', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
















        // Dữ liệu biểu đồ doanh thu
        $revenueData = DB::table('payment as p')
            ->join('reservation as r', 'p.ReservationID', '=', 'r.ReservationID')
            ->where('p.Status', 'Đã thanh toán')
            ->whereIn('r.Status', ['Đã xác nhận', 'Đã hoàn tất'])
            ->whereBetween('p.CreatedAt', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(p.CreatedAt, '{$selectFormat}') as label"),
                DB::raw('SUM(p.Amount) as revenue'),
                DB::raw('COUNT(DISTINCT r.ReservationID) as orders'),
                DB::raw('MIN(p.CreatedAt) as raw_date')
            )
            ->groupBy(DB::raw("DATE_FORMAT(p.CreatedAt, '{$selectFormat}')"))
            ->orderBy('raw_date')
            ->get();








        // Tạo dữ liệu đầy đủ cho tất cả các điểm thời gian
        $completeRevenueData = [];




        if ($period === 'today') {
            for ($hour = 0; $hour < 24; $hour++) {
                $label = sprintf("%02d:00", $hour);
                $found = $revenueData->firstWhere('label', $label);
                $completeRevenueData[] = [
                    'label' => $label,
                    'revenue' => $found ? $found->revenue : 0,
                    'orders' => $found ? $found->orders : 0
                ];
            }
        } elseif ($period === 'week') {
            for ($day = 0; $day < 7; $day++) {
                $date = $startDate->copy()->addDays($day);
                $label = $date->format('d/m');
                $found = $revenueData->firstWhere('label', $label);
                $completeRevenueData[] = [
                    'label' => $label,
                    'revenue' => $found ? $found->revenue : 0,
                    'orders' => $found ? $found->orders : 0
                ];
            }
        } elseif ($period === 'month') {
            $daysInMonth = $startDate->daysInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $startDate->copy()->addDays($day - 1);
                $label = $date->format('d/m');
                $found = $revenueData->firstWhere('label', $label);
                $completeRevenueData[] = [
                    'label' => $label,
                    'revenue' => $found ? $found->revenue : 0,
                    'orders' => $found ? $found->orders : 0
                ];
            }
        } elseif ($period === 'year') {
            for ($month = 1; $month <= 12; $month++) {
                $date = $startDate->copy()->month($month);
                $label = $date->format('m/Y');
                $found = $revenueData->firstWhere('label', $label);
                $completeRevenueData[] = [
                    'label' => $label,
                    'revenue' => $found ? $found->revenue : 0,
                    'orders' => $found ? $found->orders : 0
                ];
            }
        }








        // Thống kê theo trạng thái đơn hàng
        $orderStatus = Reservation::whereBetween('CreatedAt', [$startDate, $endDate])
            ->select('Status', DB::raw('count(*) as total'))
            ->whereNotIn('Status', ['Đã hủy'])
            ->groupBy('Status')
            ->get();




        $topDishes = DB::table('reservation_item as ri')
            ->join('menuitem as m', 'ri.ItemID', '=', 'm.ItemID')
            ->join('reservation as r', 'ri.ReservationID', '=', 'r.ReservationID')
            ->join('payment as p', 'r.ReservationID', '=', 'p.ReservationID')
            ->whereBetween('r.CreatedAt', [$startDate, $endDate])
            ->where('r.Status', 'Đã hoàn tất')
            ->where('p.Status', 'Đã thanh toán')
            ->select(
                'm.ItemName',
                'm.Price',
                DB::raw('SUM(ri.Quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT r.ReservationID) as order_count'),
                DB::raw('SUM(ri.Quantity * m.Price) as total_revenue')
            )
            ->groupBy('m.ItemID', 'm.ItemName', 'm.Price')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();




        return view('admin.statistics.index', compact(
            'statistics',
            'completeRevenueData',
            'topCustomers',
            'orderStatus',
            'period',
            'startDate',
            'endDate',
            'topDishes'
        ));
    }
}

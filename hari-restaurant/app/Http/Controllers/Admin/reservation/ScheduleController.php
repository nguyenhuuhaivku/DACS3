<?php




namespace App\Http\Controllers\admin\reservation;




use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use Carbon\Carbon;




class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Lấy ngày được chọn hoặc mặc định là ngày hiện tại
        $selectedDate = $request->date ? Carbon::parse($request->date) : now();
        $today = now();

        // Xử lý chuyển tháng
        if ($request->has('direction')) {
            if ($request->direction === 'next') {
                // Cho phép chuyển tháng tiếp theo không giới hạn
                $selectedDate = $selectedDate->copy()->addMonth()->startOfMonth();
                return redirect()->route('admin.reservations.schedule', ['date' => $selectedDate->format('Y-m-d')]);
            }

            if ($request->direction === 'prev') {
                // Chỉ cho phép quay lại nếu không phải tháng hiện tại
                if ($selectedDate->format('Y-m') !== $today->format('Y-m')) {
                    $selectedDate = $selectedDate->copy()->subMonth()->startOfMonth();
                    return redirect()->route('admin.reservations.schedule', ['date' => $selectedDate->format('Y-m-d')]);
                }
            }
        }

        // Tạo mảng các ngày
        $dates = collect();

        if ($selectedDate->format('Y-m') === $today->format('Y-m')) {
            // Nếu đang xem tháng hiện tại, chỉ hiển thị từ ngày hiện tại đến cuối tháng
            $startDate = $today;
            $endDate = $today->copy()->endOfMonth();
        } else {
            // Nếu xem các tháng khác, hiển thị toàn bộ tháng
            $startDate = $selectedDate->copy()->startOfMonth();
            $endDate = $selectedDate->copy()->endOfMonth();
        }

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates->push($date->copy());
        }


        // Đếm số đơn đặt bàn cho mỗi ngày
        $reservationCounts = Reservation::whereIn('Status', ['Chờ xác nhận', 'Đã xác nhận', 'Khách đã đến', 'Đang phục vụ'])
            ->whereMonth('ReservationDate', $selectedDate->month)
            ->whereYear('ReservationDate', $selectedDate->year)
            ->selectRaw('DATE(ReservationDate) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');


        // Lấy danh sách đặt bàn cho ngày được chọn
        $reservations = Reservation::with([
            'table',
            'user',
            'payment',
            'cartItems.menuItem'
        ])
            ->whereIn('Status', ['Chờ xác nhận', 'Đã xác nhận', 'Khách đã đến', 'Đang phục vụ'])
            ->whereDate('ReservationDate', $selectedDate)
            ->orderBy('ReservationDate', 'asc')
            ->get();


        $availableTables = Table::where('Status', 'Trống')->get();


        return view('admin.reservations.schedule', compact(
            'reservations',
            'availableTables',
            'dates',
            'reservationCounts',
            'selectedDate'
        ));
    }




    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();


            $reservation = Reservation::findOrFail($id);
            $payment = Payment::where('ReservationID', $id)->first();




            // Validate input
            if ($request->status === 'Đã xác nhận' && !$request->TableID) {
                throw new \Exception('Vui lòng chọn bàn trước khi xác nhận.');
            }




            switch ($request->status) {
                case 'Đã xác nhận':
                    // Kiểm tra bàn có tồn tại và trống không
                    $table = Table::findOrFail($request->TableID);
                    if ($table->Status !== 'Trống') {
                        throw new \Exception('Bàn đã được đặt hoặc đang bảo trì.');
                    }




                    // Kiểm tra thanh toán
                    if (
                        $payment->PaymentMethod !== 'Thanh toán tại nhà hàng'
                        && $payment->Status !== 'Đã thanh toán'
                    ) {
                        throw new \Exception('Đơn hàng chưa được thanh toán.');
                    }




                    // Cập nhật trạng thái bàn
                    $table->update(['Status' => 'Đang sử dụng']);


                    // Cập nhật reservation
                    $reservation->update([
                        'Status' => 'Đã xác nhận',
                        'TableID' => $request->TableID
                    ]);
                    break;




                case 'Khách đã đến':
                    if ($reservation->Status !== 'Đã xác nhận') {
                        throw new \Exception('Đơn phải được xác nhận trước.');
                    }
                    $reservation->update([
                        'Status' => 'Khách đã đến',
                        'CheckInTime' => now()
                    ]);
                    break;




                case 'Đang phục vụ':
                    if ($reservation->Status !== 'Khách đã đến') {
                        throw new \Exception('Khách chưa đến nhà hàng.');
                    }
                    $reservation->update(['Status' => 'Đang phục vụ']);
                    break;




                case 'Đã hoàn tất':
                    if ($reservation->Status !== 'Đang phục vụ') {
                        throw new \Exception('Đơn chưa được phục vụ xong.');
                    }


                    if ($payment->PaymentMethod === 'Thanh toán tại nhà hàng') {
                        $payment->update(['Status' => 'Đã thanh toán']);
                    }


                    Table::where('TableID', $reservation->TableID)
                        ->update(['Status' => 'Trống']);


                    $reservation->update([
                        'Status' => 'Đã hoàn tất',
                        'CheckOutTime' => now()
                    ]);
                    break;




                case 'Đã hủy':
                    if ($reservation->TableID) {
                        Table::where('TableID', $reservation->TableID)
                            ->update(['Status' => 'Trống']);
                    }
                    $reservation->update(['Status' => 'Đã hủy']);
                    break;
            }




            DB::commit();
            return redirect()->route('admin.reservations.schedule')
                ->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.reservations.schedule')
                ->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}

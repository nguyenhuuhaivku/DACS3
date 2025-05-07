<?php

namespace App\Http\Controllers\User;

use DateTime;
use App\Models\Table;
use App\Models\user\Cart;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ReservationController extends Controller
{
    public function create()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->whereNull('ReservationID')
            ->with(['menuItem' => function ($query) {
                $query->select('ItemID', 'ItemName', 'Price', 'ImageURL');
            }])
            ->get();

        // Xử lý URL hình ảnh
        $cartItems->each(function ($item) {
            if ($item->menuItem && $item->menuItem->ImageURL && !str_starts_with($item->menuItem->ImageURL, 'http')) {
                $item->menuItem->ImageURL = asset($item->menuItem->ImageURL);
            }
        });

        // Lấy thông tin người dùng và đơn đặt bàn gần nhất
        $user = Auth::user();
        $lastReservation = Reservation::where('UserID', Auth::id())
            ->orderBy('CreatedAt', 'desc')
            ->first();

        return view('user.reservation.create', compact('cartItems', 'user', 'lastReservation'));
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validate request với các rule chi tiết
            $request->validate([
                'FullName' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50',
                    'regex:/^[\p{L}\s]+$/u',
                    function ($attribute, $value, $fail) {
                        if (preg_match('/\s{2,}/', $value)) {
                            $fail('Tên không được chứa nhiều khoảng trắng liên tiếp.');
                        }
                        if (trim($value) !== $value) {
                            $fail('Tên không được chứa khoảng trắng ở đầu hoặc cuối.');
                        }
                    },
                ],
                'Phone' => [
                    'required',
                    'string',
                    'size:10',
                    'regex:/^(0)[0-9]{9}$/',
                ],
                'GuestCount' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],
                'ReservationDate' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        $reservationDate = new DateTime($value);
                        $now = new DateTime();
                        $minTime = (clone $now)->modify('+30 minutes');
                        $maxDate = (clone $now)->modify('+30 days');

                        // Kiểm tra thời gian tối thiểu
                        if ($reservationDate < $minTime) {
                            $fail('Thời gian đặt bàn phải sau thời điểm hiện tại ít nhất 30 phút.');
                            return;
                        }


                        // Kiểm tra thời gian tối đa
                        if ($reservationDate > $maxDate) {
                            $fail('Thời gian đặt bàn không được quá 30 ngày kể từ hiện tại.');
                            return;
                        }


                        // Kiểm tra giờ làm việc theo ngày trong tuần
                        $dayOfWeek = (int)$reservationDate->format('N'); // 1-7 (Thứ 2 - Chủ nhật)
                        $hour = (int)$reservationDate->format('H');
                        $minute = (int)$reservationDate->format('i');

                        // Thứ 7 và Chủ nhật (6,7)
                        if (in_array($dayOfWeek, [6, 7])) {
                            if ($hour < 8 || ($hour == 23 && $minute > 0) || $hour > 23) {
                                $fail('Thời gian đặt bàn cho cuối tuần phải từ 08:00 đến 23:00.');
                                return;
                            }
                        }
                        // Các ngày trong tuần
                        else {
                            if ($hour < 8 || ($hour == 22 && $minute > 0) || $hour > 22) {
                                $fail('Thời gian đặt bàn các ngày trong tuần phải từ 08:00 đến 22:00.');
                                return;
                            }
                        }


                        // Kiểm tra không được đặt vào quá khứ
                        if ($reservationDate < $now) {
                            $fail('Không thể đặt bàn cho thời gian đã qua.');
                            return;
                        }
                    },
                ],
                'Note' => 'nullable|string|max:200',
            ], [
                'FullName.required' => 'Vui lòng nhập họ tên.',
                'FullName.min' => 'Họ tên phải có ít nhất 2 ký tự.',
                'FullName.max' => 'Họ tên không được vượt quá 50 ký tự.',
                'FullName.regex' => 'Họ tên chỉ được chứa chữ cái và khoảng trắng.',

                'Phone.required' => 'Vui lòng nhập số điện thoại.',
                'Phone.size' => 'Số điện thoại phải có 10 số.',
                'Phone.regex' => 'Số điện thoại không hợp lệ (phải bắt đầu bằng số 0).',

                'GuestCount.required' => 'Vui lòng nhập số người.',
                'GuestCount.integer' => 'Số người phải là số nguyên.',
                'GuestCount.min' => 'Số người phải ít nhất là 1.',
                'GuestCount.max' => 'Số người không được vượt quá 20.',

                'ReservationDate.required' => 'Vui lòng chọn thời gian đặt bàn.',
                'ReservationDate.date' => 'Thời gian không hợp lệ.',
                'ReservationDate.after' => 'Thời gian đặt bàn phải sau thời điểm hiện tại.',
                'ReservationDate.before' => 'Thời gian đặt bàn không được quá 30 ngày.',

                'Note.max' => 'Ghi chú không được vượt quá 200 ký tự.',
            ]);


            do {
                $reservationCode = 'RES' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            } while (Reservation::where('ReservationCode', $reservationCode)->exists());


            // Tạo đơn đặt bàn
            $reservation = Reservation::create([
                'ReservationCode' => $reservationCode,
                'UserID' => Auth::id(),
                'FullName' => $request->FullName,
                'Phone' => $request->Phone,
                'GuestCount' => $request->GuestCount,
                'ReservationDate' => $request->ReservationDate,
                'Note' => $request->Note,
                'Status' => 'Tạm thời'
            ]);
            DB::commit();


            return redirect()->route('payment.show', ['reservation' => $reservation->ReservationID]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    public function history()
    {
        $reservations = Reservation::with(['payment', 'reservationItems.menuItem', 'table'])
            ->where('UserID', Auth::id())
            ->whereNotIn('Status', ['Tạm thời', 'Chờ thanh toán'])
            ->orderBy('CreatedAt', 'desc')
            ->get();
        return view('user.reservation.history', compact('reservations'));
    }


    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $reservation = Reservation::findOrFail($id);

            // Kiểm tra quyền hủy đơn
            if ($reservation->UserID !== Auth::id()) {
                throw new \Exception('Bạn không có quyền hủy đơn này.');
            }

            // Kiểm tra trạng thái đơn
            if (!in_array($reservation->Status, ['Chờ xác nhận', 'Đã xác nhận'])) {
                throw new \Exception('Không thể hủy đơn ở trạng thái này.');
            }

            // Cập nhật trạng thái đơn
            $reservation->Status = 'Đã hủy';
            $reservation->save();

            // Nếu đã có bàn được đặt, cập nhật trạng thái bàn
            if ($reservation->TableID) {
                Table::where('TableID', $reservation->TableID)
                    ->update(['Status' => 'Trống']);
            }

            // Cập nhật trạng thái thanh toán nếu có
            if ($reservation->payment) {
                $reservation->payment->Status = 'Đã hủy';
                $reservation->payment->save();
            }

            DB::commit();
            return back()->with('success', 'Đã hủy đơn thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}

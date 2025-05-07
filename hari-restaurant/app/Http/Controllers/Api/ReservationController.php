<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    /**
     * Get all reservations for authenticated user
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            
            $reservations = DB::table('reservation')
                ->where('UserID', $userId)
                ->orderBy('ReservationDate', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $reservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a new reservation
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'guest_count' => 'required|integer|min:1',
                'reservation_date' => 'required|date|after:now',
                'phone' => 'required|string|max:15',
                'note' => 'nullable|string',
                'items' => 'nullable|array',
                'items.*.id' => 'required|exists:menuitem,ItemID',
                'items.*.quantity' => 'required|integer|min:1'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $validator->errors()->all()),
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $userId = Auth::id();
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập hoặc phiên đăng nhập hết hạn'
                ], 401);
            }
            
            // Kiểm tra nếu người dùng đã đặt bàn trong thời gian tương tự
            $reservationDate = new \DateTime($request->reservation_date);
            $reservationDateStart = (clone $reservationDate)->modify('-1 hour')->format('Y-m-d H:i:s');
            $reservationDateEnd = (clone $reservationDate)->modify('+1 hour')->format('Y-m-d H:i:s');
            
            $existingReservation = DB::table('reservation')
                ->where('UserID', $userId)
                ->where('Status', '!=', 'Đã hủy')
                ->where('ReservationDate', '>=', $reservationDateStart)
                ->where('ReservationDate', '<=', $reservationDateEnd)
                ->first();
            
            if ($existingReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã có đơn đặt bàn trong khung giờ này. Vui lòng chọn thời gian khác.'
                ], 400);
            }
            
            // Tạo mã đặt bàn ngẫu nhiên
            $reservationCode = 'RES' . strtoupper(Str::random(6));
            
            // Tạo đặt bàn mới
            $reservationId = DB::table('reservation')->insertGetId([
                'ReservationCode' => $reservationCode,
                'UserID' => $userId,
                'FullName' => $user->name,
                'Phone' => $request->phone,
                'GuestCount' => $request->guest_count,
                'ReservationDate' => $request->reservation_date,
                'Status' => 'Chờ xác nhận',
                'Note' => $request->note,
                'CreatedAt' => now(),
                'UpdatedAt' => now()
            ]);
            
            // Thêm món ăn vào đơn đặt bàn nếu có
            if ($request->has('items') && !empty($request->items)) {
                foreach ($request->items as $item) {
                    // Lấy thông tin món ăn
                    $menuItem = DB::table('menuitem')
                        ->where('ItemID', $item['id'])
                        ->first();
                    
                    if ($menuItem) {
                        DB::table('reservation_item')->insert([
                            'ReservationID' => $reservationId,
                            'ItemID' => $item['id'],
                            'Quantity' => $item['quantity'],
                            'Price' => $menuItem->Price,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'is_initial_order' => 1
                        ]);
                    }
                }
            }
            
            // Lấy thông tin đặt bàn vừa tạo
            $reservation = DB::table('reservation')
                ->where('ReservationID', $reservationId)
                ->first();
            
            // Tạo mã thanh toán cho đơn đặt bàn - hỗ trợ thanh toán sau
            $paymentCode = 'PAY-' . strtoupper(Str::random(6));
            $paymentId = DB::table('payment')->insertGetId([
                'PaymentCode' => $paymentCode,
                'ReservationID' => $reservationId,
                'Amount' => 0, // Sẽ được cập nhật sau khi xác nhận đơn
                'PaymentMethod' => 'Thanh toán tại nhà hàng',
                'Status' => 'Chờ thanh toán',
                'CreatedAt' => now(),
                'UpdatedAt' => now()
            ]);
            
            // Cập nhật tổng tiền cho payment dựa trên món đã đặt
            $totalAmount = DB::table('reservation_item')
                ->where('ReservationID', $reservationId)
                ->join('menuitem', 'reservation_item.ItemID', '=', 'menuitem.ItemID')
                ->sum(DB::raw('reservation_item.Price * reservation_item.Quantity'));
            
            DB::table('payment')
                ->where('PaymentID', $paymentId)
                ->update(['Amount' => $totalAmount]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đặt bàn thành công',
                'data' => $reservation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific reservation
     */
    public function show($id)
    {
        try {
            $userId = Auth::id();
            
            $reservation = DB::table('reservation')
                ->where('ReservationID', $id)
                ->where('UserID', $userId)
                ->first();
            
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn đặt bàn'
                ], 404);
            }
            
            // Lấy thông tin món ăn đã đặt
            $items = DB::table('reservation_item')
                ->join('menuitem', 'reservation_item.ItemID', '=', 'menuitem.ItemID')
                ->where('reservation_item.ReservationID', $id)
                ->select(
                    'reservation_item.*',
                    'menuitem.ItemName as name',
                    'menuitem.ImageURL as image'
                )
                ->get();
            
            // Tính tổng tiền
            $total = $items->sum(function($item) {
                return $item->Price * $item->Quantity;
            });
            
            // Thông tin thanh toán
            $payment = DB::table('payment')
                ->where('ReservationID', $id)
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'reservation' => $reservation,
                    'items' => $items,
                    'total' => $total,
                    'payment' => $payment
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
     * Cancel a reservation
     */
    public function cancel($id)
    {
        try {
            $userId = Auth::id();
            
            $reservation = DB::table('reservation')
                ->where('ReservationID', $id)
                ->where('UserID', $userId)
                ->first();
            
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn đặt bàn'
                ], 404);
            }
            
            // Kiểm tra xem có thể hủy đơn không
            if (in_array($reservation->Status, ['Đã hoàn tất', 'Đã hủy'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy đơn đặt bàn với trạng thái: ' . $reservation->Status
                ], 400);
            }
            
            // Cập nhật trạng thái
            DB::table('reservation')
                ->where('ReservationID', $id)
                ->update([
                    'Status' => 'Đã hủy',
                    'UpdatedAt' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hủy đơn đặt bàn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
} 
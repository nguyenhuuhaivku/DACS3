<?php


namespace App\Http\Controllers\Admin;


use Log;
use PDF;
use App\Models\User;
use App\Models\Table;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\user\MenuItem;
use App\Models\ReservationItem;
use App\Models\user\MenuCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentRejectionNotification;
use Carbon\Carbon;




class InvoiceController extends Controller
{
    public function current(Request $request)
    {
        $query = Payment::with(['reservation.user', 'reservation.cartItems.menuItem'])
            ->whereHas('reservation', function ($query) {
                $query->whereNotIn('Status', ['Đã hoàn tất', 'Đã hủy']);
            });


        // Lọc theo thời gian
        if ($request->period) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('CreatedAt', today());
                    break;
                case 'week':
                    $query->whereBetween('CreatedAt', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('CreatedAt', now()->month)
                        ->whereYear('CreatedAt', now()->year);
                    break;
                case 'custom':
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('CreatedAt', [$request->start_date, $request->end_date]);
                    }
                    break;
            }
        }


        // Lọc theo phương thức thanh toán
        if ($request->payment_method) {
            $query->where('PaymentMethod', $request->payment_method);
        }


        // Lọc theo trạng thái
        if ($request->status) {
            $query->where('Status', $request->status);
        }


        $invoices = $query->orderBy('CreatedAt', 'desc')->paginate(10);
        return view('admin.invoices.current', compact('invoices'));
    }


    public function paid()
    {
        $invoices = Payment::with([
            'reservation',
            'reservation.table',
            'reservation.reservationItems.menuItem'
        ])
            ->where('Status', 'Đã thanh toán')
            ->orderBy('CreatedAt', 'desc')
            ->paginate(10);


        return view('admin.invoices.paid', compact('invoices'));
    }


    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->Status = $request->status;
        $payment->UpdatedAt = now();
        $payment->save();


        // Cập nhật trạng thái đặt bàn khi thanh toán
        if ($request->status == 'Đã thanh toán') {
            $reservation = Reservation::find($payment->ReservationID);
            if ($reservation) {
                if ($payment->PaymentMethod == 'Thanh toán tại nhà hàng') {
                    $reservation->Status = 'Đã hoàn tất';
                } else {
                    $reservation->Status = 'Chờ xác nhận';
                }
                $reservation->UpdatedAt = now();
                $reservation->save();
            }
        }


        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');
    }




    public function preview($id)
    {
        try {
            $invoice = Payment::with([
                'reservation.user',
                'reservation.table',
                'reservation.reservationItems.menuItem'
            ])->findOrFail($id);


            // Lấy các món ban đầu
            $paidItems = $invoice->reservation->reservationItems()
                ->where('is_initial_order', true)
                ->with('menuItem')
                ->get();


            // Lấy các món thêm sau
            $newItems = $invoice->reservation->reservationItems()
                ->where('is_initial_order', false)
                ->with('menuItem')
                ->get();


            $paidAmount = $paidItems->sum(function ($item) {
                return $item->Price * $item->Quantity;
            });


            $newAmount = $newItems->sum(function ($item) {
                return $item->Price * $item->Quantity;
            });


            return view('admin.invoices.preview', compact(
                'invoice',
                'paidItems',
                'newItems',
                'paidAmount',
                'newAmount'
            ))->render();
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Có lỗi xảy ra khi tải hóa đơn: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getInvoiceDetails($id)
    {
        $invoice = Payment::with([
            'reservation.user',
            'reservation.table',
            'reservation.reservationItems.menuItem'
        ])->findOrFail($id);
        $paymentConfirmTime = $invoice->UpdatedAt;
        // Tách các món thành hai nhóm: đã thanh toán và mới thêm
        $paidItems = $invoice->reservation->reservationItems
            ->where('created_at', '<=', $paymentConfirmTime);
        $newItems = $invoice->reservation->reservationItems
            ->where('created_at', '>', $paymentConfirmTime);


        $paidAmount = $paidItems->sum(function ($item) {
            return $item->Price * $item->Quantity;
        });


        $newAmount = $newItems->sum(function ($item) {
            return $item->Price * $item->Quantity;
        });


        return [
            'invoice' => $invoice,
            'paidItems' => $paidItems,
            'newItems' => $newItems,
            'paidAmount' => $paidAmount,
            'newAmount' => $newAmount
        ];
    }
    public function export($id)
    {
        $invoice = Payment::with([
            'reservation',
            'reservation.table',
            'reservation.reservationItems.menuItem'
        ])->findOrFail($id);


        $pdf = PDF::loadView('admin.invoices.pdf', compact('invoice'));


        return $pdf->download('invoice-' . $invoice->PaymentID . '.pdf');
    }




    public function confirm(Payment $payment)
    {
        try {
            DB::beginTransaction();


            // Cập nhật trạng thái thanh toán
            $payment->update([
                'Status' => 'Đã thanh toán',
                'UpdatedAt' => now()
            ]);


            // Nếu đơn đặt bàn đang chờ xác nhận, tự động cập nhật trạng thái
            if ($payment->reservation->Status === 'Chờ thanh toán') {
                $payment->reservation->update([
                    'Status' => 'Chờ xác nhận'
                ]);
            }


            DB::commit();


            return back()->with('success', 'Đã xác nhận thanh toán thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    public function completed(Request $request)
    {
        $query = Payment::with(['reservation.table', 'reservation.user'])
            ->whereHas('reservation', function ($query) {
                $query->where('Status', 'Đã hoàn tất');
            });


        // Xử lý lọc theo thời gian
        $period = $request->input('period', 'today');

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;

            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;

            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;

            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;

            case 'custom':
                $startDate = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : Carbon::today();
                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : Carbon::today()->endOfDay();
                break;

            default:
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
        }


        $query->whereBetween('CreatedAt', [$startDate, $endDate]);


        $invoices = $query->orderBy('CreatedAt', 'desc')
            ->paginate(10)
            ->appends($request->all()); // Giữ lại các tham số lọc khi phân trang


        return view('admin.invoices.completed', compact('invoices'));
    }


    public function getCategories()
    {
        return MenuCategory::all();
    }




    public function getItems(Request $request)
    {
        $query = MenuItem::query();
        if ($request->has('category')) {
            $query->where('CategoryID', $request->category);
        }


        return $query->get();
    }


    public function addItem(Request $request)
    {
        try {
            DB::beginTransaction();




            $payment = Payment::findOrFail($request->payment_id);


            // Kiểm tra món ăn có sẵn
            $menuItem = MenuItem::findOrFail($request->item_id);
            if (!$menuItem->Available) {
                throw new \Exception('Món ăn này hiện không có sẵn');
            }




            // Thêm món mới với is_initial_order = false
            $reservationItem = ReservationItem::create([
                'ReservationID' => $request->reservation_id,
                'ItemID' => $request->item_id,
                'Quantity' => $request->quantity,
                'Price' => $menuItem->Price,
                'is_initial_order' => false  // Đánh dấu là món thêm mới
            ]);




            // Cập nhật tổng tiền trong payment
            $payment->Amount += ($menuItem->Price * $request->quantity);
            $payment->save();




            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'Thêm món thành công',
                'payment_id' => $payment->PaymentID
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function reject(Payment $payment)
    {
        try {
            DB::beginTransaction();


            // Cập nhật trạng thái thanh toán
            $payment->update([
                'Status' => 'Từ chối',
                'UpdatedAt' => now()
            ]);


            // Cập nhật trạng thái đơn đặt bàn
            $payment->reservation->update([
                'Status' => 'Đã hủy'
            ]);


            // Xóa các reservation items
            ReservationItem::where('ReservationID', $payment->ReservationID)->delete();


            // Gửi email thông báo
            try {
                Mail::to($payment->reservation->user->email)
                    ->send(new PaymentRejectionNotification($payment, $payment->reservation));
            } catch (\Exception $e) {
            }
            DB::commit();
            return back()->with('success', 'Đã từ chối đơn thanh toán và gửi email thông báo!');
        } catch (\Exception $e) {
            DB::rollBack();


            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $tables = Table::where('Status', 'Trống')->get();
        $menuItems = MenuItem::where('Available', true)->get();


        return view('admin.invoices.create', compact('customers', 'tables', 'menuItems'));
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();


            // Tạo reservation mới
            $reservation = Reservation::create([
                'UserID' => $request->customer_id,
                'TableID' => $request->table_id,
                'ReservationDate' => now(),
                'Status' => 'Đang phục vụ',
                'CheckInTime' => now()
            ]);
            // Tính tổng tiền
            $total = 0;
            foreach ($request->items as $index => $itemId) {
                $menuItem = MenuItem::find($itemId);
                $quantity = $request->quantities[$index];
                $total += $menuItem->Price * $quantity;




                // Tạo reservation item
                ReservationItem::create([
                    'ReservationID' => $reservation->ReservationID,
                    'ItemID' => $itemId,
                    'Quantity' => $quantity,
                    'Price' => $menuItem->Price
                ]);
            }


            // Tạo payment
            $payment = Payment::create([
                'PaymentCode' => $this->generatePaymentCode(),
                'ReservationID' => $reservation->ReservationID,
                'Amount' => $total,
                'PaymentMethod' => $request->payment_method,
                'Status' => 'Chờ thanh toán'
            ]);




            DB::commit();
            return redirect()->route('admin.invoices.current')
                ->with('success', 'Tạo hóa đơn thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

<?php


namespace App\Http\Controllers;


use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\user\Cart;
use App\Mail\ReservationConfirmation;
use Illuminate\Support\Facades\Mail;


class PaymentController extends Controller
{
    public function show(Reservation $reservation)
    {
        // Debug log
        Log::info('Payment Show Called', [
            'reservation_id' => $reservation->ReservationID,
            'user_id' => Auth::id()
        ]);


        // Kiểm tra quyền truy cập
        if ($reservation->UserID !== Auth::id()) {
            abort(403);
        }


        // Lấy thông tin giỏ hàng với eager loading menuItem
        $cartItems = Cart::with('menuItem')
            ->where('user_id', Auth::id())
            ->whereNull('ReservationID')
            ->get();




        // Tính tổng tiền
        $total = $cartItems->sum(function ($item) {
            return $item->menuItem->Price * $item->quantity;
        });


        $hasItems = $cartItems->count() > 0;


        return view('user.payment.show', [
            'reservation' => $reservation,
            'cartItems' => $cartItems,
            'total' => $total,
            'hasItems' => $hasItems
        ]);
    }


    protected function generatePaymentCode()
    {
        do {
            $code = 'PAY-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (Payment::where('PaymentCode', $code)->exists());




        return $code;
    }


    public function process(Request $request, Reservation $reservation)
    {
        try {
            DB::beginTransaction();




            // Kiểm tra trạng thái reservation
            if ($reservation->Status !== 'Tạm thời') {
                throw new \Exception('Đơn đặt bàn không hợp lệ.');
            }


            // Lấy thông tin giỏ hàng
            $cartItems = Cart::with('menuItem')
                ->where('user_id', Auth::id())
                ->whereNull('ReservationID')
                ->get();


            // Tạo payment record
            $payment = Payment::create([
                'PaymentCode' => $this->generatePaymentCode(),
                'ReservationID' => $reservation->ReservationID,
                'Amount' => $request->total,
                'PaymentMethod' => $request->payment_method,
                'Status' => 'Chờ thanh toán'
            ]);


            if ($request->payment_method === 'Thanh toán tại nhà hàng') {
                // Liên kết cart items với reservation và tạo reservation items
                foreach ($cartItems as $cartItem) {
                    $cartItem->update(['ReservationID' => $reservation->ReservationID]);
                    \App\Models\ReservationItem::create([
                        'ReservationID' => $reservation->ReservationID,
                        'ItemID' => $cartItem->item_id,
                        'Quantity' => $cartItem->quantity,
                        'Price' => $cartItem->menuItem->Price
                    ]);
                }


                // Cập nhật trạng thái đơn
                $reservation->update(['Status' => 'Chờ xác nhận']);




                // Xóa giỏ hàng và cập nhật session
                Cart::where('user_id', Auth::id())
                    ->where('ReservationID', $reservation->ReservationID)
                    ->delete();
                session()->forget('cart_count');


                DB::commit();
                return redirect()->route('payment.success', ['payment' => $payment->PaymentID]);
            } else if ($request->payment_method === 'Chuyển khoản ngân hàng') {
                // Cập nhật trạng thái đơn đặt bàn
                $reservation->Status = 'Chờ thanh toán';
                $reservation->save();


                DB::commit();
                return redirect()->route('payment.bank-transfer', $payment->PaymentID);
            }


            throw new \Exception('Phương thức thanh toán không hợp lệ.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment process error', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->ReservationID
            ]);
            return back()->with('error', 'Có lỗi xảy ra trong quá trình thanh toán: ' . $e->getMessage());
        }
    }


    public function success(Payment $payment)
    {
        try {
            // Kiểm tra quyền truy cập
            if ($payment->reservation->UserID !== Auth::id()) {
                abort(403);
            }


            // Log truy cập trang success
            Log::info('Accessing payment success page', [
                'payment_id' => $payment->PaymentID,
                'user_id' => Auth::id()
            ]);


            // Gửi email thông báo
            try {
                $user_email = $payment->reservation->user->email;
                Log::info('Attempting to send email to: ' . $user_email);

                Mail::to($user_email)
                    ->send(new ReservationConfirmation($payment->reservation));

                Log::info('Confirmation email sent successfully');
            } catch (\Exception $e) {
                Log::error('Failed to send confirmation email', [
                    'error' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString(),
                    'reservation_id' => $payment->ReservationID,
                    'user_email' => $payment->reservation->user->email ?? 'null'
                ]);
                // Có thể thêm thông báo cho người dùng
                session()->flash('mail_error', 'Không thể gửi email xác nhận. Vui lòng liên hệ hỗ trợ.');
            }


            return view('user.payment.success', [
                'reservation' => $payment->reservation,
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            Log::error('Error in payment success page', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->PaymentID
            ]);
            return redirect()->route('home')
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán');
        }
    }


    public function bankTransfer(Payment $payment)
    {
        try {
            if ($payment->reservation->UserID !== Auth::id()) {
                abort(403);
            }
            // Lấy thông tin giỏ hàng
            $cartItems = Cart::with('menuItem')
                ->where('user_id', Auth::id())
                ->whereNull('ReservationID')
                ->get();


            if ($cartItems->isEmpty()) {
                Log::warning('Empty cart items in bank transfer page', [
                    'payment_id' => $payment->PaymentID,
                    'user_id' => Auth::id()
                ]);
            }


            return view('user.payment.bank-transfer', [
                'payment' => $payment,
                'reservation' => $payment->reservation,
                'cartItems' => $cartItems,
                'total' => $payment->Amount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bank transfer page', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->PaymentID
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi hiển thị trang thanh toán');
        }
    }


    public function confirm(Request $request, Payment $payment)
    {
        try {
            DB::beginTransaction();




            // Kiểm tra quyền truy cập
            if ($payment->reservation->UserID !== Auth::id()) {
                abort(403);
            }




            // Validate với thông báo tiếng Việt
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'payment_proof.required' => 'Vui lòng tải lên ảnh biên lai thanh toán',
                'payment_proof.image' => 'File phải là định dạng hình ảnh',
                'payment_proof.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg',
                'payment_proof.max' => 'Kích thước ảnh không được vượt quá 2MB'
            ]);



            // Xử lý upload ảnh
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');


                // Tạo tên file unique với timestamp
                $fileName = time() . '_' . $payment->PaymentCode . '.' . $file->getClientOriginalExtension();


                // Lưu file vào thư mục storage/app/public/payment_proofs
                $path = $file->storeAs('payment_proofs', $fileName, 'public');


                if (!$path) {
                    throw new \Exception('Không thể lưu file. Vui lòng thử lại.');
                }
            } else {
                throw new \Exception('Không tìm thấy file biên lai thanh toán');
            }

            // Lấy thông tin giỏ hàng
            $cartItems = Cart::with('menuItem')
                ->where('user_id', Auth::id())
                ->whereNull('ReservationID')
                ->get();

            // Cập nhật thông tin payment
            $payment->PaymentProof = $path;
            $payment->Status = 'Chờ thanh toán';
            $payment->save();

            // Tạo reservation items
            foreach ($cartItems as $cartItem) {
                \App\Models\ReservationItem::create([
                    'ReservationID' => $payment->ReservationID,
                    'ItemID' => $cartItem->item_id,
                    'Quantity' => $cartItem->quantity,
                    'Price' => $cartItem->menuItem->Price
                ]);
            }


            // Cập nhật trạng thái đặt bàn
            $payment->reservation->Status = 'Chờ xác nhận';
            $payment->reservation->save();


            // Xóa giỏ hàng và cập nhật session
            Cart::where('user_id', Auth::id())
                ->whereNull('ReservationID')
                ->delete();



            session(['cart_count' => 0]);

            DB::commit();


            return redirect()->route('payment.success', ['payment' => $payment->PaymentID])
                ->with('success', 'Xác nhận thanh toán thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment confirmation failed', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->PaymentID ?? null,
                'user_id' => Auth::id()
            ]);




            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

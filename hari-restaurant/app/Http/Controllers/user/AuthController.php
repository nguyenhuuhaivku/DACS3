<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('user')->check()) {
            return redirect()->route('home');
        }
        return view('user.auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::guard('user')->attempt($credentials)) {
            $user = Auth::guard('user')->user();
            if ($user->roles !== 'User') {
                Auth::guard('user')->logout();
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Vui lòng sử dụng tài khoản người dùng.');
            }
            $request->session()->regenerate();
            return redirect()->route('home');
        }
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => 'User',
        ]);
        // Gửi email chào mừng
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            // Log lỗi nếu cần
        }
        // Auth::guard('user')->login($user);
        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
    public function showForgotPasswordForm()
    {
        return view('user.auth.forgot-password');
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        // Tạo mã xác nhận 6 chữ số
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu mã xác nhận vào cache với thời gian sống là 15 phút
        Cache::put('password_reset_' . $request->email, $resetCode, now()->addMinutes(15));
        // Gửi email
        try {
            Mail::to($request->email)->send(new ResetPasswordMail($resetCode, $request->email));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể gửi email. Vui lòng thử lại sau.');
        }
        return redirect()->route('password.reset.code')->with([
            'email' => $request->email,
            'success' => 'Mã xác nhận đã được gửi đến email của bạn!'
        ]);
    }
    public function showResetCodeForm()
    {
        return view('user.auth.reset-code');
    }
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $storedCode = Cache::get('password_reset_' . $request->email);
        if (!$storedCode || $storedCode !== $request->code) {
            return back()->with('error', 'Mã xác nhận không đúng hoặc đã hết hạn');
        }

        // Cập nhật mật khẩu
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        // Xóa mã khỏi cache
        Cache::forget('password_reset_' . $request->email);
        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công!');
    }
}

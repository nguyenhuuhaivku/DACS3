<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }


    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user using the admin guard
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();

            // Check if the authenticated user has the 'Admin' role
            if ($admin->role !== 'Admin') {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Bạn không có quyền truy cập.',
                ]);
            }

            // Regenerate the session to prevent session fixation attacks
            $request->session()->regenerate();

            // Update the admin's last login information
            $admin->update([
                'last_login_ip' => $request->ip(),
                'last_login_at' => now(),
            ]);
            // Redirect to the intended admin dashboard route
            return redirect()->route('admin.dashboard');
        }

        // If authentication fails, redirect back with an error message
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()->route('admin.login');
    }
}

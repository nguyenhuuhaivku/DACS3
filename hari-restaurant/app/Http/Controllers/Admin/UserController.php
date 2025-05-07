<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $request->roles,
        ]);

        return redirect()->route('admin.users.UserManagement')->with('success', 'Thêm người dùng thành công');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'roles' => 'required'
        ]);

        $user = User::findOrFail($id);

        if (Auth::id() == $user->id && $request->roles !== $user->roles) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'roles' => $request->roles,
            ]);

            // Đăng xuất
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return response('<script>
            if (window.self !== window.top) {
                window.top.location.href = "' . route('login') . '";
            } else {
                window.location.href = "' . route('login') . '";
            }
        </script>', 200, ['Content-Type' => 'text/html']);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'roles' => $request->roles,
        ]);

        return redirect()->route('admin.users.UserManagement')->with('success', 'Sửa người dùng thành công');
    }



    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('admin.users.UserManagement')->with('success', 'Xóa người dùng thành công');
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Kiểm tra URL để xác định đang truy cập admin hay user
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            return route('dang-nhap');
        }
        return null;
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\user\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/'; // Trang chủ sau khi đăng nhập
    public static function redirectTo()
    {
        return route('dang-nhap');
    }
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Route API prefix
        Route::prefix('api')
            ->middleware('api')
            ->namespace('App\Http\Controllers\Api')
            ->group(base_path('routes/api.php'));
        
        // Chia sẻ số lượng giỏ hàng với tất cả các view
        View::composer('*', function ($view) {
            $cartCount = 0;

            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
            }

            $view->with('cartCount', $cartCount);
        });
    }
}

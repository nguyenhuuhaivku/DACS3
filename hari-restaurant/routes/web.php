<?php

use App\Models\user\MenuItem;
use Illuminate\Support\Facades\Route;
use App\http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\table\TableController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\reservation\HistoryController;
use App\Http\Controllers\Admin\reservation\ScheduleController;
use App\Http\Controllers\TakeawayController;
use App\Http\Controllers\OrderController;

// Routes công khai
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/search', [MenuController::class, 'search'])->name('menu.search');
Route::get('/about', function () {
    return view('user.about');
});
// Routes xác thực cho Users
Route::middleware('guest:user')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('dang-nhap');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login');
    Route::post('/dang-ki', [AuthController::class, 'register'])->name('dang-ki');
});
Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/quen-mat-khau', [AuthController::class, 'forgotPassword'])->name('password.email');

Route::get('/quen-mat-khau/xac-nhan', [AuthController::class, 'showResetCodeForm'])->name('password.reset.code');
Route::post('/quen-mat-khau/xac-nhan', [AuthController::class, 'verifyResetCode'])->name('password.reset.verify');


// Routes cho User đã đăng nhập
Route::middleware(['auth:user'])->group(function () {
    Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('dang-xuat');
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reservation routes
    Route::get('/reservation/create', [App\Http\Controllers\User\ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation/create', [App\Http\Controllers\User\ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservation/history', [App\Http\Controllers\User\ReservationController::class, 'history'])->name('reservations.history');
    Route::put('/reservations/{reservation}/cancel', [App\Http\Controllers\User\ReservationController::class, 'cancel'])->name('user.reservations.cancel');

    // Cart routes
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/info', [App\Http\Controllers\CartController::class, 'info'])->name('cart.info');

    // Payment routes
    Route::get('/payment/{reservation}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{reservation}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/bank-transfer/{payment}', [PaymentController::class, 'bankTransfer'])->name('payment.bank-transfer');
    Route::get('/payment/success/{payment}', [PaymentController::class, 'success'])->name('payment.success');
    Route::post('/payment/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');

    // Takeaway routes
    Route::get('/takeaway', [TakeawayController::class, 'index'])->name('takeaway.index');
    Route::post('/takeaway', [TakeawayController::class, 'store'])->name('takeaway.store');
    Route::get('/takeaway/confirmation/{order}', [TakeawayController::class, 'confirmation'])->name('takeaway.confirmation');
    Route::get('/takeaway/history', [TakeawayController::class, 'history'])->name('takeaway.history');
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Routes cho Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });
    // Protected routes
    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
        // User management routes
        Route::resource('users', UserController::class);

        // Table management routes
        Route::resource('tables', TableController::class);

        // Reservation management routes
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
            Route::put('/{id}/status', [ScheduleController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/history', [HistoryController::class, 'index'])->name('history');
        });
        // Menu management routes
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::resource('categories', MenuCategoryController::class);
            Route::resource('items', MenuItemController::class);
        });

        // Support routes
        Route::get('/support', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('support');
        Route::get('/support/{id}', [App\Http\Controllers\Admin\ChatController::class, 'show'])->name('support.show');
        Route::post('/support/{id}/reply', [App\Http\Controllers\Admin\ChatController::class, 'reply'])->name('support.reply');
        Route::get('/menu-categories', [InvoiceController::class, 'getCategories']);
        Route::get('/menu-items', [InvoiceController::class, 'getItems']);
        // Invoice routes
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/current', [InvoiceController::class, 'current'])->name('current');

            Route::post('/add-item', [InvoiceController::class, 'addItem']);
            // Route::get('/paid', [InvoiceController::class, 'paid'])->name('paid');
            Route::get('/completed', [InvoiceController::class, 'completed'])->name('completed');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');

            Route::put('/{id}/status', [InvoiceController::class, 'updateStatus'])->name('updateStatus');
            Route::put('/{payment}/confirm', [InvoiceController::class, 'confirm'])
                ->name('confirm');
            Route::put('/{payment}/reject', [InvoiceController::class, 'reject'])->name('reject');

            Route::get('/{id}/preview', [InvoiceController::class, 'preview'])->name('preview');
            Route::get('/{id}/export', [InvoiceController::class, 'export'])->name('export');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        });

        // Takeaway orders management routes
        Route::prefix('takeaway-orders')->name('takeaway-orders.')->group(function () {
            Route::get('/current', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'current'])->name('current');
            Route::get('/completed', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'completed'])->name('completed');
            Route::get('/search', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'search'])->name('search');
            Route::get('/{order}', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'show'])->name('show');
            Route::put('/{order}/update-status', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'updateStatus'])->name('update-status');
            Route::put('/{order}/update-delivery-time', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'updateDeliveryTime'])->name('update-delivery-time');
            Route::get('/{order}/export', [App\Http\Controllers\Admin\TakeawayOrderController::class, 'export'])->name('export');
        });
    });
});

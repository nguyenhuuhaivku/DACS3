<head>
    <link rel="stylesheet" href="{{ secure_asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ secure_asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<header class="flex items-center justify-between fixed w-full z-50 navbar">
    <div class="container mx-auto px-8 flex items-center">
        <!-- Logo Section -->
        <div class="logo-container">
            <div class="logo-inner">
                <i class="fas fa-utensils"></i>
                <h1>Hari Restaurant</h1>
            </div>
            <div class="logo-glow"></div>
        </div>
        <!-- Navigation -->
        <nav class="main-nav ml-10">
            <ul class="flex space-x-6 items-center">
                <li><a href="/" class="nav-link">Trang chủ</a></li>
                <li><a href="/menu" class="nav-link">Thực đơn</a></li>
                <li><a href="/reservation/create" class="nav-link">Đặt bàn</a></li>
                <li><a href="/reservation/history" class="nav-link">Đơn đặt bàn</a></li>
                <li><a href="/about" class="nav-link">Về chúng tôi</a></li>
            </ul>
        </nav>
    </div>
    <!-- Right Section -->
    <div class="flex items-center gap-4 pr-8">
        <!-- Cart Icon -->
        <div class="cart-wrapper">
            <a href="#" id="cart-icon" class="cart-button">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" class="cart-count {{ $cartCount }}">{{ $cartCount }}</span>
            </a>
        </div>
        @auth('user')
            <div class="user-profile">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::guard('user')->user()->name }}</span>
                </div>
                <form action="{{ route('dang-xuat') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-button">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('dang-nhap') }}" class="login-button">
                <span class="login-button-content">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Đăng nhập</span>
                </span>
            </a>
        @endauth
    </div>
</header>

<div id="cart-overlay"
    class="fixed inset-0 bg-black opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-40"></div>

<div id="cart-panel"
    class="fixed top-0 right-0 h-full w-96 bg-white/95 backdrop-blur-md shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">
    <div class="flex flex-col h-full">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white/80">
            <div class="flex items-center gap-3">
                <i class="fas fa-shopping-basket text-2xl text-orange-400"></i>
                <h2 class="text-xl font-semibold text-gray-800">Thực Đơn Của Bạn</h2>
            </div>
            <button id="close-cart" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
            </button>
        </div>

        <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-4">
            {{-- chứa dữ liệu --}}
        </div>

        <div class="border-t border-gray-200 p-6 bg-white/80">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <i class="fas fa-receipt text-orange-400 text-xl"></i>
                    <span class="text-lg font-semibold text-gray-800">Tổng cộng:</span>
                </div>
                <span id="cart-total" class="text-xl font-bold text-orange-500"></span>
            </div>
            <a href="{{ route('reservation.create') }}"
                class="w-full bg-gradient-to-r from-orange-400 to-orange-500 text-white text-center py-4 rounded-lg
                      hover:from-orange-500 hover:to-orange-600 transition-all transform hover:-translate-y-0.5
                      flex items-center justify-center gap-2 shadow-lg">
                <i class="fas fa-utensils"></i>
                Đặt bàn ngay
            </a>
        </div>
    </div>
</div>

<script src="{{ secure_asset('js/header.js') }}"></script>
<script src="{{ asset('js/header.js') }}"></script>

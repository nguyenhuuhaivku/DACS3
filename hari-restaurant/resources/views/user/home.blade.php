@extends('layouts.my_website')

@section('title', 'Hari Restaurant')

@section('content')
<section class="hero-section relative flex items-center justify-center">

    <!-- Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center" data-aos="fade-up">
        <h1 class="text-6xl md:text-7xl font-playfair mb-6 text-white leading-tight tracking-wide">
            Hari Restaurant
        </h1>

        <p class="text-xl md:text-2xl text-gray-200 mb-8 font-light max-w-2xl mx-auto leading-relaxed">
            Trải nghiệm ẩm thực đẳng cấp với hương vị tinh tế
        </p>

        <!-- Decorative line -->
        <div class="flex items-center justify-center mb-10">
            <div class="h-px w-12 bg-orange-400"></div>
            <div class="mx-4">
                <i class="fas fa-utensils text-orange-400"></i>
            </div>
            <div class="h-px w-12 bg-orange-400"></div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col md:flex-row gap-6 justify-center items-center mt-12">
            <!-- Nút THỰC ĐƠN -->
            <a href="menu" class="menu-btn group relative overflow-hidden">
                <span class="relative z-10 flex items-center">
                    <i class="fas fa-book-open mr-2"></i>
                    THỰC ĐƠN
                </span>
                <span class="btn-border"></span>
            </a>

            <!-- Nút ĐẶT BÀN -->
            <a href="reservation/create" class="reservation-btn group relative overflow-hidden">
                <span class="relative z-10 flex items-center">
                    <i class="fas fa-concierge-bell mr-2"></i>
                    ĐẶT BÀN
                </span>
                <span class="btn-border"></span>
            </a>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-10  transform  animate-bounce">
            <div class="text-white text-center">
                <div class="text-sm tracking-wider">Đặt Ngay</div>
                <i class="fas fa-chevron-down mt-2"></i>
            </div>
        </div>
    </div>
</section>
@endsection
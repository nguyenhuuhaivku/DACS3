<head>
    <link rel="stylesheet" href="{{ secure_asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&display=swap"
        rel="stylesheet">
</head>
@extends('layouts.pages')


@section('content')
    <section class="menu-section">
        <div class="container mx-auto px-4">
            <div class="menu-header">
                <div class="filter-container">
                    <button class="filter-toggle" id="filterToggle">
                        <i class="fas fa-filter"></i>
                        Bộ lọc
                    </button>
                    <div class="filter-box" id="filterBox">
                        <div class="filter-section">
                            <h3>Khoảng giá</h3>
                            <div class="price-range">
                                <input type="range" id="priceRange" min="0" max="500000" step="10000"
                                    value="500000">
                                <div class="price-inputs">
                                    <input type="number" id="minPrice" placeholder="Từ">
                                    <span>-</span>
                                    <input type="number" id="maxPrice" placeholder="Đến">
                                </div>
                            </div>
                        </div>
                        <div class="filter-section">
                            <h3>Sắp xếp theo</h3>
                            <select id="sortBy" class="sort-select">
                                <option value="default">Mặc định</option>
                                <option value="price-asc">Giá tăng dần</option>
                                <option value="price-desc">Giá giảm dần</option>
                                <option value="name-asc">Tên A-Z</option>
                                <option value="name-desc">Tên Z-A</option>
                            </select>
                        </div>
                        <div class="filter-section">
                            <h3>Trạng thái</h3>
                            <div class="status-filters">
                                <label class="filter-checkbox">
                                    <input type="checkbox" id="availableOnly">
                                    <span>Còn món</span>
                                </label>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button id="applyFilters" class="apply-filters">Áp dụng</button>
                            <button id="resetFilters" class="reset-filters">Đặt lại</button>
                        </div>
                    </div>
                </div>
                <h2 class="menu-title">
                    Thực Đơn
                </h2>
                <div class="search-container">
                    <button class="search-icon" id="searchToggle">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="search-box" id="searchBox">
                        <input type="text" id="menu-search-input" placeholder="Tìm kiếm món ăn...">
                        <button class="search-close" id="searchClose">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Thông báo -->
            <div id="cart-success-message" class="notification-toast hidden">
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="notification-text">
                        <h4><i class="fas fa-shopping-cart mr-2"></i>Thêm Thành Công!</h4>
                        <p>Món ăn đã được thêm vào giỏ hàng</p>
                    </div>
                </div>
                <div class="notification-progress"></div>
            </div>
            <!-- Danh mục món ăn -->
            <div class="menu-categories flex flex-wrap justify-center space-x-4">
                <button class="menu-category active" data-category="all">Tất cả</button>
                @foreach ($categories as $category)
                    <button class="menu-category" data-category="{{ htmlspecialchars($category->CategoryID) }}">
                        {{ htmlspecialchars($category->CategoryName) }}
                    </button>
                @endforeach
            </div>
            <!-- Danh sách món ăn -->
            <div class="menu-category-items active" data-category="all">
                @foreach ($allItems as $item)
                    <div class="menu-item" data-name="{{ htmlspecialchars(strtolower($item->ItemName)) }}"
                        data-price="{{ (int) $item->Price }}" data-id="{{ (int) $item->ItemID }}">
                        @if ($item->status === 'Món mới')
                            <div class="menu-item-badge badge-new">
                                <i class="fas fa-star"></i> Mới
                            </div>
                        @elseif($item->status === 'Phổ biến')
                            <div class="menu-item-badge badge-popular">
                                <i class="fas fa-fire"></i> Phổ biến
                            </div>
                        @elseif($item->status === 'Đặc biệt')
                            <div class="menu-item-badge badge-special">
                                <i class="fas fa-crown"></i> Đặc biệt
                            </div>
                        @endif

                        <div class="menu-item-image">
                            <img src="{{ $item->ImageURL }}" alt="{{ $item->ItemName }}" loading="lazy">
                        </div>
                        <div class="menu-item-content">
                            <h3>
                                <i class="fas fa-utensils text-gray-400 mr-2"></i>
                                {{ $item->ItemName }}
                            </h3>
                            <p>
                                <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                                {{ $item->Description }}
                            </p>
                            <div class="menu-item-price-status">
                                <div class="menu-item-price">
                                    <i class="fas fa-tag text-gray-400 mr-2"></i>
                                    {{ number_format($item->Price) }}₫
                                </div>
                                <div class="menu-item-status">
                                    @if ($item->Available)
                                        <span class="status-available">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Còn món
                                        </span>
                                    @else
                                        <span class="status-unavailable">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Hết món
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if ($item->Available)
                                <button class="menu-item-button" data-id="{{ $item->ItemID }}"
                                    data-name="{{ $item->ItemName }}" data-price="{{ $item->Price }}"
                                    data-image="{{ $item->ImageURL }}">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            @else
                                <button class="menu-item-button disabled" disabled>
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @foreach ($categories as $category)
                <div class="menu-category-items" data-category="{{ $category->CategoryID }}">
                    @foreach ($category->menuItems as $item)
                        <div class="menu-item" data-name="{{ htmlspecialchars(strtolower($item->ItemName)) }}"
                            data-price="{{ (int) $item->Price }}" data-id="{{ (int) $item->ItemID }}">
                            @if ($item->status === 'Món mới')
                                <div class="menu-item-badge badge-new">
                                    <i class="fas fa-star"></i> Mới
                                </div>
                            @elseif($item->status === 'Phổ biến')
                                <div class="menu-item-badge badge-popular">
                                    <i class="fas fa-fire"></i> Phổ biến
                                </div>
                            @elseif($item->status === 'Đặc biệt')
                                <div class="menu-item-badge badge-special">
                                    <i class="fas fa-crown"></i> Đặc biệt
                                </div>
                            @endif
                            <div class="menu-item-image">
                                <img src="{{ $item->ImageURL }}" alt="{{ $item->ItemName }}" loading="lazy">
                            </div>
                            <div class="menu-item-content">
                                <h3>
                                    <i class="fas fa-utensils text-gray-400 mr-2"></i>
                                    {{ $item->ItemName }}
                                </h3>
                                <p>
                                    <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                                    {{ $item->Description }}
                                </p>
                                <div class="menu-item-price-status">
                                    <div class="menu-item-price">
                                        <i class="fas fa-tag text-gray-400 mr-2"></i>
                                        {{ number_format($item->Price) }}₫
                                    </div>
                                    <div class="menu-item-status">
                                        @if ($item->Available)
                                            <span class="status-available">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Còn món
                                            </span>
                                        @else
                                            <span class="status-unavailable">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Hết món
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($item->Available)
                                    <button class="menu-item-button" data-id="{{ $item->ItemID }}"
                                        data-name="{{ $item->ItemName }}" data-price="{{ $item->Price }}"
                                        data-image="{{ $item->ImageURL }}">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                @else
                                    <button class="menu-item-button disabled" disabled>
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
@endsection
<script src="{{ secure_asset('js/menu.js') }}"></script>
<script src="{{ asset('js/menu.js') }}"></script>

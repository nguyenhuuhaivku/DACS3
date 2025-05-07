@extends('layouts.pages')




@section('content')
    <section class="payment-section">
        <div class="container mx-auto px-4">
            <div class="payment-header">
                <a href="{{ route('reservation.create') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h2 class="payment-title">Thanh Toán Đơn Đặt Bàn</h2>
            </div>




            <div class="payment-container">
                <!-- Cột trái: Thông tin đặt bàn và menu -->
                <div class="payment-left-column">
                    <!-- Thông tin đặt bàn -->
                    <div class="reservation-info">
                        <h3><i class="fas fa-info-circle"></i> Thông tin đặt bàn</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Tên:</label>
                                <span>{{ $reservation->FullName }}</span>
                            </div>
                            <div class="info-item">
                                <label>SĐT:</label>
                                <span>{{ $reservation->Phone }}</span>
                            </div>
                            <div class="info-item">
                                <label>Số người:</label>
                                <span>{{ $reservation->GuestCount }}</span>
                            </div>
                            <div class="info-item">
                                <label>Thời gian:</label>
                                <span>{{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('H:i d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>




                    <!-- Danh sách món đã chọn -->
                    <div class="menu-list">
                        <h3><i class="fas fa-utensils"></i> Thực đơn đã chọn</h3>
                        <div class="menu-items-container">
                            @foreach ($cartItems as $item)
                                <div class="menu-item">
                                    @if (Str::startsWith($item->menuItem->ImageURL, 'http'))
                                        <img src="{{ $item->menuItem->ImageURL }}" alt="{{ $item->menuItem->ItemName }}">
                                    @else
                                        <img src="{{ asset($item->menuItem->ImageURL) }}"
                                            alt="{{ $item->menuItem->ItemName }}">
                                    @endif
                                    <div class="item-details">
                                        <h4>{{ $item->menuItem->ItemName }}</h4>
                                        <div class="item-quantity">x{{ $item->quantity }}</div>
                                        <div class="item-price">
                                            {{ number_format($item->menuItem->Price * $item->quantity) }}đ
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>




                <!-- Cột phải: Thanh toán -->
                <div class="payment-right-column">
                    <div class="payment-summary">
                        <h3><i class="fas fa-credit-card"></i> Thanh toán</h3>
                        <div class="summary-details">
                            <div class="summary-item">
                                <span>Tổng tiền món:</span>
                                <strong>{{ number_format($total) }}đ</strong>
                            </div>
                            <div class="summary-item">
                                <span>Phí dịch vụ:</span>
                                <strong>0đ</strong>
                            </div>
                            <div class="summary-total">
                                <span>Tổng thanh toán:</span>
                                <strong>{{ number_format($total) }}đ</strong>
                            </div>
                        </div>




                        <form action="{{ route('payment.process', $reservation->ReservationID) }}" method="POST">
                            @csrf
                            <div class="payment-methods">
                                <h3><i class="fas fa-credit-card"></i> Phương thức thanh toán</h3>
                                <div class="method-options">
                                    <label class="method-option">
                                        <input type="radio" name="payment_method" value="Thanh toán tại nhà hàng" checked>
                                        <span>
                                            <i class="fas fa-money-bill-wave"></i>
                                            Thanh toán tại nhà hàng
                                        </span>
                                    </label>
                                    <label class="method-option {{ !$hasItems ? 'disabled' : '' }}">
                                        <input type="radio" name="payment_method" value="Chuyển khoản ngân hàng"
                                            {{ !$hasItems ? 'disabled' : '' }}>
                                        <span>
                                            <i class="fas fa-university"></i>
                                            Chuyển khoản ngân hàng
                                            @if (!$hasItems)
                                                <small class="disabled-text">(Chỉ áp dụng cho đơn có món)</small>
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            </div>
                    </div>
                    <input type="hidden" name="total" value="{{ $total }}">
                    <button type="submit" class="payment-button">
                        <i class="fas fa-check"></i> Xác nhận thanh toán
                    </button>
                    </form>
            </div>
            </div>
        </div>
        </div>
    </section>
    <link rel="stylesheet" href="{{ secure_asset('css/show-payment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/show-payment.css') }}">
@endsection

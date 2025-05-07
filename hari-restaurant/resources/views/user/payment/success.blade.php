@extends('layouts.pages')


@section('content')
    <section class="success-section">
        <div class="container mx-auto px-4">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="success-title">Đặt Bàn Thành Công!</h2>
                <p class="success-message">Cảm ơn bạn đã tin tưởng nhà hàng của chúng tôi</p>


                <div class="reservation-details">
                    <div class="detail-item">
                        <i class="fas fa-receipt"></i>
                        <div class="detail-content">
                            <label>Mã đặt bàn:</label>
                            <span>#{{ $reservation->ReservationCode }}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="detail-content">
                            <label>Thời gian:</label>
                            <span>{{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <div class="detail-content">
                            <label>Số người:</label>
                            <span>{{ $reservation->GuestCount }} người</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-money-bill"></i>
                        <div class="detail-content">
                            <label>Phương thức thanh toán:</label>
                            <span>{{ $payment->PaymentMethod }}</span>
                        </div>
                    </div>
                </div>


                <div class="action-buttons">
                    <a href="{{ route('home') }}" class="home-button">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </section>


    <link rel="stylesheet" href="{{ asset('css/success-payment.css') }}">
@endsection

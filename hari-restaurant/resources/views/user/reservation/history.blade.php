@extends('layouts.pages')


@section('content')
    <section class="mt-8">
        <h2 class="history-title">
            <i class="fas fa-history"></i>
            Đơn hàng của bạn
        </h2>


        <div class="history-header">
            <div>Mã đặt bàn</div>
            <div>Thông tin đặt bàn</div>
            <div>Món đã đặt</div>
            <div>Trạng thái</div>
            <div>Thanh toán</div>
        </div>


        @foreach ($reservations as $reservation)
            <div class="history-item" data-status="{{ $reservation->Status }}">
                <div class="reservation-id">
                    #{{ $reservation->ReservationID }}
                    <div class="reservation-date">
                        {{ \Carbon\Carbon::parse($reservation->CreatedAt)->format('d/m/Y H:i') }}
                    </div>
                </div>


                <div class="reservation-info">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <span>{{ $reservation->FullName }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ $reservation->Phone }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <span>{{ $reservation->GuestCount }} người</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>


                <div class="menu-items">
                    @if ($reservation->reservationItems->isNotEmpty())
                        @foreach ($reservation->reservationItems as $item)
                            <div class="info-item">
                                <i class="fas fa-utensils"></i>
                                <span>{{ $item->menuItem->ItemName }} x {{ $item->Quantity }}</span>
                                <span class="item-price">{{ number_format($item->Price * $item->Quantity) }}đ</span>
                            </div>
                        @endforeach
                        <div class="info-item total-price">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Tổng cộng:</span>
                            <span
                                class="item-price">{{ number_format(
                                    $reservation->reservationItems->sum(function ($item) {
                                        return $item->Price * $item->Quantity;
                                    }),
                                ) }}đ</span>
                        </div>
                    @else
                        <span class="text-gray-500">Chưa đặt món</span>
                    @endif
                </div>


                <div class="status-actions">
                    <div class="status-group">
                        <span class="status-badge status-{{ strtolower($reservation->Status) }}">
                            {{ $reservation->Status }}
                        </span>


                        @if ($reservation->payment)
                            @if ($reservation->payment->PaymentMethod == 'Chuyển khoản ngân hàng')
                                @if (!$reservation->payment->PaymentProof)
                                    <span class="status-badge status-warning">Chưa tải biên lai</span>
                                @elseif($reservation->payment->Status == 'Chờ xác nhận')
                                    <span class="status-badge status-info">Chờ xác nhận thanh toán</span>
                                @elseif($reservation->payment->Status == 'Đã thanh toán')
                                    <span class="status-badge status-success">Đã thanh toán</span>
                                @endif
                            @else
                                <span
                                    class="status-badge status-{{ $reservation->payment->Status == 'Đã thanh toán' ? 'success' : 'warning' }}">
                                    {{ $reservation->payment->Status == 'Đã thanh toán' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                </span>
                            @endif
                        @else
                            <span class="status-badge status-warning">Chưa thanh toán</span>
                        @endif


                        @if ($reservation->TableID && $reservation->table)
                            <span class="status-badge status-info"><i class="fas fa-chair"></i> Bàn
                                {{ $reservation->table->TableNumber }}</span>
                        @endif
                    </div>


                    @if (in_array($reservation->Status, ['Chờ xác nhận', 'Đã xác nhận']) &&
                            (!$reservation->payment || $reservation->payment->PaymentMethod === 'Thanh toán tại nhà hàng'))
                        <form action="{{ route('user.reservations.cancel', $reservation->ReservationID) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn hủy đơn này?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn-cancel">
                                <i class="fas fa-times"></i> Hủy đơn
                            </button>
                        </form>
                    @endif
                </div>


                <div class="payment-info">
                    @if ($reservation->payment)
                        <div class="payment-amount">{{ number_format($reservation->payment->Amount) }}đ</div>
                        <div class="payment-method">{{ $reservation->payment->PaymentMethod }}</div>
                        <span class="status-badge status-{{ strtolower($reservation->payment->Status) }}">
                            {{ $reservation->payment->Status }}
                        </span>
                    @else
                        <span class="text-gray-500">Chưa thanh toán</span>
                    @endif
                </div>
            </div>
        @endforeach
    </section>


    <link rel="stylesheet" href="{{ secure_asset('css/reservation-history.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reservation-history.css') }}">
@endsection

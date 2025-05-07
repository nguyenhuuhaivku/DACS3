@extends('layouts.pages')

@section('content')
    <section class="mt-9">
        <div class="reservation-container">
            <div class="reservation-layout">
                <!-- Form đặt bàn -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-concierge-bell"></i> Thông tin đặt bàn</h2>
                    </div>
                    <div class="card-body">
                        <form id="reservation-form" action="{{ route('reservation.store') }}" method="POST">
                            @csrf
                            <div class="form-grid">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Họ tên</label>
                                    <input type="text" name="FullName"
                                        value="{{ old('FullName') ?? ($user->name ?? ($lastReservation->FullName ?? '')) }}"
                                        class="form-input @error('FullName') is-invalid @enderror" required>
                                    @error('FullName')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Số điện thoại</label>
                                    <input type="text" name="Phone"
                                        value="{{ old('Phone') ?? ($user->phone ?? ($lastReservation->Phone ?? '')) }}"
                                        class="form-input @error('Phone') is-invalid @enderror" required>
                                    @error('Phone')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-users"></i> Số người(1 - 20)</label>
                                    <input type="number" name="GuestCount"
                                        value="{{ old('GuestCount') ?? ($lastReservation->GuestCount ?? '1') }}"
                                        class="form-input @error('GuestCount') is-invalid @enderror" min="1"
                                        required>
                                    @error('GuestCount')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt"></i> Thời gian</label>
                                    <input type="datetime-local" name="ReservationDate" value="{{ old('ReservationDate') }}"
                                        class="form-input @error('ReservationDate') is-invalid @enderror" required>
                                    @error('ReservationDate')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-comment"></i> Ghi chú</label>
                                <textarea name="Note" rows="3" class="form-input">{{ old('Note') ?? ($lastReservation->Note ?? '') }}</textarea>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="submit-button">
                                    <i class="fas fa-check-circle"></i> Xác nhận
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Thông tin món -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-utensils"></i> Món ăn đặt kèm</h2>
                        <a href="{{ route('menu') }}" class="add-more-btn">
                            <i class="fas fa-plus-circle"></i> Chọn món
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="reservation-items-container">
                            <div class="reservation-items">
                                @forelse($cartItems as $item)
                                    @if ($item->menuItem)
                                        <div class="reservation-item" data-id="{{ $item->id }}">
                                            <img src="{{ $item->menuItem->ImageURL }}"
                                                alt="{{ $item->menuItem->ItemName }}" class="reservation-item-image"
                                                onerror="handleImageError(this)">
                                            <div class="reservation-item-details">
                                                <h3>{{ $item->menuItem->ItemName }}</h3>
                                                <div class="reservation-item-price"
                                                    data-price="{{ $item->menuItem->Price }}">
                                                    {{ number_format($item->menuItem->Price) }} VNĐ
                                                </div>
                                                <div class="reservation-quantity-control">
                                                    <button class="reservation-update-quantity qty-btn"
                                                        data-action="decrease" data-id="{{ $item->id }}"> <i
                                                            class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" value="{{ $item->quantity }}" min="1"
                                                        class="reservation-quantity-input" data-id="{{ $item->id }}">
                                                    <button class="reservation-update-quantity qty-btn"
                                                        data-action="increase" data-id="{{ $item->id }}"> <i
                                                            class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <button class="reservation-delete-item" data-id="{{ $item->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                @empty
                                    <div class="reservation-empty">
                                        <i class="fas fa-shopping-basket"></i>
                                        <p class="text-white">Bạn có thể chọn món ăn đặt kèm trước khi đặt bàn</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if ($cartItems->isNotEmpty())
                            <div class="reservation-footer">
                                <div class="reservation-total">
                                    <span><i class="fa-solid fa-money-bill"></i> Tổng cộng:</span>
                                    <span class="reservation-total-amount">
                                        {{ number_format(
                                            $cartItems->sum(function ($item) {
                                                return $item->menuItem ? $item->menuItem->Price * $item->quantity : 0;
                                            }),
                                        ) }}
                                        VNĐ
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <link rel="stylesheet" href="{{ secure_asset('css/reservation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reservation.css') }}">
@endsection

<script src="{{ secure_asset('js/reservation.js') }}"></script>
<script src="{{ asset('js/reservation.js') }}"></script>

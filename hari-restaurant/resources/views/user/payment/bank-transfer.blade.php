@extends('layouts.pages')


@section('content')
    <section class="payment-section">
        <div class="container">
            <div class="payment-grid">
                <div class="left-column">
                    <div class="order-info">
                        <h3><i class="fas fa-info-circle"></i> Thông tin đơn đặt bàn</h3>
                        <div class="info-content">
                            <div class="info-item">
                                <label>Mã đơn:</label>
                                <span>#{{ $reservation->ReservationID }}</span>
                            </div>
                            <div class="info-item">
                                <label>Tên:</label>
                                <span>{{ $reservation->FullName }}</span>
                            </div>
                            <div class="info-item">
                                <label>SĐT:</label>
                                <span>{{ $reservation->Phone }}</span>
                            </div>
                            <div class="info-item">
                                <label>Thời gian:</label>
                                <span>{{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('H:i d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>


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
                                            {{ number_format($item->menuItem->Price * $item->quantity) }}đ</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="payment-summary">
                        <h3><i class="fas fa-credit-card"></i> Thông tin thanh toán</h3>
                        <div class="summary-content">
                            <div class="summary-item">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($total) }}đ</span>
                            </div>
                            <div class="summary-item">
                                <span>Phí dịch vụ:</span>
                                <span>0đ</span>
                            </div>
                            <div class="summary-total">
                                <span>Tổng cộng:</span>
                                <span>{{ number_format($total) }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- cot. phai? -->
                <div class="right-column">
                    <div class="payment-methods">
                        <div class="qr-section">
                            <h3><i class="fas fa-qrcode"></i> Quét mã QR</h3>
                            <div class="qr-container">
                                <img src="https://img.vietqr.io/image/MB-0966994591-compact.png?amount={{ $total }}&addInfo=THANHTOAN_{{ $reservation->ReservationID }}&accountName=NGUYEN%20HUU%20HAI"
                                    alt="QR Code" class="qr-code">
                            </div>
                        </div>


                        <div class="manual-section">
                            <h3><i class="fas fa-university"></i> Hoặc chuyển khoản thủ công</h3>
                            <div class="bank-details">
                                <div class="bank-row">
                                    <div class="bank-item">
                                        <label>Ngân hàng</label>
                                        <span>MB Bank</span>
                                    </div>
                                    <div class="bank-item">
                                        <label>Số tài khoản</label>
                                        <div class="copy-wrapper">
                                            <span>0966994591</span>
                                            <button class="copy-btn" onclick="copyToClipboard('0966994591')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="bank-row">
                                    <div class="bank-item">
                                        <label>Chủ tài khoản</label>
                                        <span>NGUYEN HUU HAI</span>
                                    </div>
                                    <div class="bank-item">
                                        <label>Số tiền</label>
                                        <div class="copy-wrapper">
                                            <span>{{ number_format($total) }}đ</span>
                                            <button class="copy-btn" onclick="copyToClipboard('{{ $total }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="bank-row">
                                    <div class="bank-item full-width">
                                        <label>Nội dung chuyển khoản</label>
                                        <div class="copy-wrapper">
                                            <span>THANHTOAN_{{ $reservation->ReservationID }}</span>
                                            <button class="copy-btn"
                                                onclick="copyToClipboard('THANHTOAN_{{ $reservation->ReservationID }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Nút xác nhận -->
            <div class="confirmation-section">
                <form action="{{ route('payment.confirm', $payment->PaymentID) }}" method="POST"
                    enctype="multipart/form-data" id="payment-form">
                    @csrf
                    <div class="upload-wrapper">
                        <input type="file" id="payment_proof" name="payment_proof" accept=".jpg,.jpeg,.png ,.HEIC"
                            class="file-input hidden" required>
                        <label for="payment_proof" class="upload-label">
                            <i class="fas fa-upload"></i>
                            <span>Tải lên ảnh biên lai (bắt buộc)</span>
                        </label>
                        <div id="image-preview" class="mt-3 hidden">
                            <img src="" alt="Preview" class="max-w-xs ">
                        </div>
                        @if ($errors->any())
                            <div class="error-messages">
                                @foreach ($errors->all() as $error)
                                    <div class="error-message">{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="back-nav">
                        <a href="{{ route('payment.show', $reservation->ReservationID) }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            <span>Quay lại</span>
                        </a>
                        <button type="submit" class="confirm-button" id="confirm-btn">
                            <i class="fas fa-check"></i> Xác nhận đã chuyển khoản
                        </button>
                    </div>
                </form>
            </div>


            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </section>


    <link rel="stylesheet" href="{{ asset('css/bank-transfer.css') }}">


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('payment-form');
            const fileInput = document.getElementById('payment_proof');
            const confirmBtn = document.getElementById('confirm-btn');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Ngăn form submit mặc định

                if (!fileInput.files || fileInput.files.length === 0) {
                    alert('Vui lòng tải lên ảnh biên lai thanh toán');
                    return;
                }

                // Disable nút submit để tránh submit nhiều lần
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                // Submit form
                this.submit();
            });

            // Xử lý preview ảnh
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('image-preview');
                        preview.classList.remove('hidden');
                        preview.querySelector('img').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
        // Xử lý copy
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const button = event.currentTarget;
                button.classList.add('copy-success');
                const icon = button.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                setTimeout(() => {
                    button.classList.remove('copy-success');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 1000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
                alert('Không thể sao chép. Vui lòng thử lại.');
            });
        }
    </script>


    <style>
        .error-messages {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            border-radius: 0.375rem;
        }


        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }


        .confirm-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }


        .hidden {
            display: none;
        }


        .alert {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 0.375rem;
        }


        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #dc2626;
        }
    </style>


@endsection

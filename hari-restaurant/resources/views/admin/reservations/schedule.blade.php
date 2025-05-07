@extends('layouts.app')


@section('content')
    <div class="p-2">
        <h2>Quản lý lịch đặt bàn</h2>


        <!-- Phần filter container -->
        <div class="filter-container mb-4">
            <div class="date-navigation d-flex align-items-center gap-3 mb-3">
                <a href="{{ route('admin.reservations.schedule', ['date' => $selectedDate->format('Y-m-d'), 'direction' => 'prev']) }}"
                    class="btn btn-outline-primary btn-sm {{ $selectedDate->format('Y-m') === now()->format('Y-m') ? 'disabled' : '' }}">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <h5 class="mb-0">{{ $selectedDate->format('m/Y') }}</h5>
                <a href="{{ route('admin.reservations.schedule', ['date' => $selectedDate->format('Y-m-d'), 'direction' => 'next']) }}"
                    class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>


            <div class="dates-container">
                @foreach ($dates as $date)
                    <a href="{{ route('admin.reservations.schedule', ['date' => $date->format('Y-m-d')]) }}"
                        class="btn {{ $date->format('Y-m-d') === $selectedDate->format('Y-m-d') ? 'btn-primary' : 'btn-outline-secondary' }}
                            date-button position-relative text-decoration-none">
                        <div class="date-label">{{ $date->format('d/m') }}</div>
                        <div class="day-label">{{ __($date->locale('vi')->format('D')) }}</div>
                        @if ($reservationCounts[$date->format('Y-m-d')] ?? 0 > 0)
                            <span class="badge bg-danger">
                                {{ $reservationCounts[$date->format('Y-m-d')] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>


        <!-- Bảng hiện tại của bạn -->
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th title="Mã đặt bàn">
                            <i class="fas fa-hashtag"></i>
                        </th>
                        <th title="Khách hàng">
                            <i class="fas fa-user"></i>
                        </th>
                        <th title="Số điện thoại">
                            <i class="fas fa-phone"></i>
                        </th>
                        <th title="Số khách">
                            <i class="fas fa-users"></i>
                        </th>
                        <th title="Thời gian">
                            <i class="fas fa-clock"></i>
                        </th>
                        <th title="Bàn">
                            <i class="fas fa-chair"></i>
                        </th>
                        <th title="Trạng thái">
                            <i class="fas fa-info-circle"></i>
                        </th>
                        <th title="Ghi chú">
                            <i class="fas fa-sticky-note"></i>
                        </th>
                        <th title="Món ăn">
                            <i class="fas fa-utensils"></i>
                        </th>
                        <th title="Thanh toán">
                            <i class="fas fa-money-bill-wave"></i>
                        </th>
                        <th title="Thao tác">
                            <i class="fas fa-cogs"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td>#{{ $reservation->ReservationCode }}</td>
                            <td>{{ $reservation->FullName }}</td>
                            <td>{{ $reservation->Phone }}</td>
                            <td>{{ $reservation->GuestCount }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($reservation->table)
                                    <i class="fas fa-chair"></i> {{ $reservation->table->TableNumber }}
                                @else
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge bg-{{ $reservation->Status === 'Chờ xác nhận'
                                        ? 'warning'
                                        : ($reservation->Status === 'Đã xác nhận'
                                            ? 'info'
                                            : ($reservation->Status === 'Khách đã đến'
                                                ? 'primary'
                                                : ($reservation->Status === 'Đang phục vụ'
                                                    ? 'success'
                                                    : 'secondary'))) }}">
                                    {{ $reservation->Status }}
                                </span>
                            </td>
                            <td>
                                @if ($reservation->Note)
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#noteModal{{ $reservation->ReservationID }}">
                                        <i class="fas fa-sticky-note"></i> Xem
                                    </button>




                                    <!-- Modal hiển thị ghi chú -->
                                    <div class="modal fade" id="noteModal{{ $reservation->ReservationID }}">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ghi chú của khách hàng</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-start">{{ $reservation->Note }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($reservation->reservationItems->isNotEmpty())
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#menuModal{{ $reservation->ReservationID }}">
                                        <i class="fas fa-eye"></i> {{ $reservation->reservationItems->count() }} món
                                    </button>




                                    <!-- Modal hiển thị món ăn -->
                                    <div class="modal fade" id="menuModal{{ $reservation->ReservationID }}">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Danh sách món đã đặt</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>STT</th>
                                                                    <th>Món</th>
                                                                    <th>Số lượng</th>
                                                                    <th>Đơn giá</th>
                                                                    <th>Thành tiền</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($reservation->reservationItems as $index => $item)
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td>{{ $item->menuItem->ItemName }}</td>
                                                                        <td>{{ $item->Quantity }}</td>
                                                                        <td>{{ number_format($item->Price) }}đ</td>
                                                                        <td>{{ number_format($item->Price * $item->Quantity) }}đ
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="table-info">
                                                                    <td colspan="4" class="text-end"><strong>Tổng
                                                                            cộng:</strong></td>
                                                                    <td><strong>{{ number_format(
                                                                        $reservation->reservationItems->sum(function ($item) {
                                                                            return $item->Price * $item->Quantity;
                                                                        }),
                                                                    ) }}đ</strong>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($reservation->payment)
                                    <span
                                        class="badge bg-{{ $reservation->payment->Status === 'Đã thanh toán' ? 'success' : 'warning' }}">
                                        {{ $reservation->payment->Status }}
                                        <br>
                                        <small>{{ $reservation->payment->PaymentMethod }}</small>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.reservations.updateStatus', $reservation->ReservationID) }}"
                                    method="POST" class="d-flex flex-column align-items-center gap-2">
                                    @csrf
                                    @method('PUT')




                                    @switch($reservation->Status)
                                        @case('Chờ xác nhận')
                                            <div class="mb-2 w-100">
                                                <select name="TableID" class="form-select form-select-sm" required>
                                                    <option value="">-- Chọn bàn --</option>
                                                    @foreach ($availableTables as $table)
                                                        @if ($table->Seats >= $reservation->GuestCount)
                                                            <option value="{{ $table->TableID }}">
                                                                Bàn {{ $table->TableNumber }}
                                                                ({{ $table->Seats }} ghế - {{ $table->Location }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="d-flex gap-2 w-100">
                                                <button type="submit" name="status" value="Đã xác nhận"
                                                    class="btn btn-success btn-sm flex-grow-1">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        @break

                                        @case('Đã xác nhận')
                                            <button type="submit" name="status" value="Khách đã đến"
                                                class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-user-check"></i> Khách đến
                                            </button>
                                        @break

                                        @case('Khách đã đến')
                                            <button type="submit" name="status" value="Đang phục vụ"
                                                class="btn btn-info btn-sm w-100">
                                                <i class="fas fa-utensils"></i> Bắt đầu phục vụ
                                            </button>
                                        @break

                                        @case('Đang phục vụ')
                                            <button type="submit" name="status" value="Đã hoàn tất"
                                                class="btn btn-success btn-sm w-100">
                                                <i class="fas fa-check-circle"></i> Hoàn tất
                                            </button>
                                        @break
                                    @endswitch
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>




    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            .table th {
                background-color: #f8f9fa;
                vertical-align: middle;
                text-align: center;
            }




            .table td {
                vertical-align: middle;
                text-align: center;
            }




            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                min-width: 38px;
            }




            .form-select-sm {
                min-width: 150px;
            }




            .gap-2 {
                gap: 0.5rem !important;
            }




            td form {
                min-height: 100%;
                padding: 0.5rem 0;
            }




            .d-flex {
                width: 100%;
            }




            .d-flex button {
                flex: 1;
            }




            [title] {
                cursor: help;
            }


            .filter-container {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }


            .date-navigation {
                justify-content: center;
                margin-bottom: 20px;
            }


            .dates-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
                gap: 10px;
                width: 100%;
            }


            .date-button {
                height: 70px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 8px;
                border-radius: 8px;
                transition: all 0.2s ease;
                position: relative;
                text-decoration: none !important;
            }


            .date-button:hover {
                transform: translateY(-2px);
                z-index: 1;
            }


            .date-button.btn-primary {
                background-color: #007bff;
                border-color: #007bff;
                color: white !important;
            }


            .date-button.btn-outline-secondary {
                color: #6c757d;
            }


            .date-button.btn-outline-secondary:hover {
                background-color: #f8f9fa;
                color: #495057;
            }


            .date-label {
                font-weight: bold;
                font-size: 14px;
                margin-bottom: 2px;
            }


            .day-label {
                font-size: 12px;
                text-transform: uppercase;
            }


            .badge {
                position: absolute;
                top: -5px;
                right: -5px;
                font-size: 10px;
                padding: 4px 6px;
                border-radius: 10px;
            }


            /* Responsive adjustments */
            @media (max-width: 768px) {
                .dates-container {
                    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
                    gap: 8px;
                }


                .date-button {
                    height: 60px;
                    padding: 6px;
                }


                .date-label {
                    font-size: 13px;
                }


                .day-label {
                    font-size: 11px;
                }
            }
        </style>
    @endpush


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const activeButton = document.querySelector('.btn-primary');
                if (activeButton) {
                    activeButton.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });
        </script>
    @endpush
@endsection

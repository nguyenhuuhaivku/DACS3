@extends('adminlte::page')

@section('title', 'Chi tiết khách hàng')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Chi tiết khách hàng: {{ $customer->name }}</h1>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thông tin cơ bản</h3>
                </div>
                <div class="card-body">
                    <p><strong>Tên:</strong> {{ $customer->name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                    <p><strong>Ngày tham gia:</strong> {{ date('d/m/Y', strtotime($customer->created_at)) }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thống kê</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total_reservations'] }}</h3>
                                    <p>Tổng số đặt bàn</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_spent']) }}đ</h3>
                                    <p>Tổng chi tiêu</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($stats['avg_spending']) }}đ</h3>
                                    <p>Trung bình/lần</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Món ăn yêu thích</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($stats['favorite_items'] as $item)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $item->ItemName }}</h5>
                                        <small class="text-muted">{{ number_format($item->Price) }}đ/món</small>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-primary badge-pill">
                                            Đặt {{ $item->total_quantity }} lần
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            trong {{ $item->order_count }} đơn
                                        </small>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lịch sử đặt bàn</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($reservations as $reservation)
                            <div>
                                <i class="fas fa-calendar bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i>
                                        {{ date('d/m/Y H:i', strtotime($reservation->ReservationDate)) }}
                                    </span>
                                    <h3 class="timeline-header">
                                        Đặt bàn #{{ $reservation->ReservationID }}
                                    </h3>
                                    <div class="timeline-body">
                                        <p><strong>Trạng thái:</strong> {{ $reservation->Status }}</p>
                                        <p><strong>Số người:</strong> {{ $reservation->GuestCount }}</p>
                                        <p><strong>Tổng tiền:</strong>
                                            {{ number_format($reservation->payment->Amount ?? 0) }}đ
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .content-header {
            padding: 15px;
        }

        .btn-secondary {
            margin-right: 15px;
        }

        .timeline {
            max-height: 340px;
            overflow-y: auto;
            padding-right: 10px;
            scrollbar-width: thin;
            scrollbar-color: #3498db transparent;
        }

        .timeline::-webkit-scrollbar {
            width: 6px;
        }


        .timeline::-webkit-scrollbar-track {
            background: transparent;
        }


        .timeline::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 3px;
        }

        .timeline-item {
            margin-left: 60px;
            margin-right: 15px;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .timeline>div {
            margin-bottom: 15px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Add any JavaScript functionality here
        });
    </script>
@stop

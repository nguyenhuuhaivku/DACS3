@extends('adminlte::page')

@section('title', 'Thống kê')
















@section('content_header')
<h1>Thống kê tổng quan</h1>
@stop
















@section('content')
<div class="container-fluid">
    <!-- Bộ lọc thời gian -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.statistics') }}" method="GET" class="row">
                <div class="col-md-2">
                    <select name="period" class="form-control" id="period">
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Năm này</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Tùy chọn</option>
                    </select>
                </div>
                <div class="col-md-3 custom-date {{ request('period') == 'custom' ? '' : 'd-none' }}">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3 custom-date {{ request('period') == 'custom' ? '' : 'd-none' }}">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </form>
        </div>
    </div>
















    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($statistics['total_orders']) }}</h3>
                    <p>Tổng đơn đặt bàn</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($statistics['total_revenue']) }}đ</h3>
                    <p>Doanh thu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($statistics['total_customers']) }}</h3>
                    <p>Khách hàng mới</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($statistics['average_order_value']) }}đ</h3>
                    <p>Giá trị đơn trung bình</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
















    <div class="row">
        <!-- Biểu đồ doanh thu -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Biểu đồ doanh thu</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
















        <!-- Top khách hàng -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 5 khách hàng</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Số đơn</th>
                                <th>Tổng chi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topCustomers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->reservations_count }}</td>
                                <td>{{ number_format($customer->total_spent) }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
















    <!-- Biểu đồ trạng thái đơn hàng -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trạng thái đơn hàng</h3>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 5 món ăn phổ biến</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tên món</th>
                                    <th>Số lượng đã bán</th>
                                    <th>Số đơn có món</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topDishes as $dish)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-{{ $loop->iteration <= 3 ? 'warning' : 'secondary' }} mr-2">
                                                #{{ $loop->iteration }}
                                            </span>
                                            {{ $dish->ItemName }}
                                        </div>
                                    </td>
                                    <td class="text-center">{{ number_format($dish->total_quantity) }}</td>
                                    <td class="text-center">{{ number_format($dish->order_count) }}</td>
                                    <td class="text-right">{{ number_format($dish->total_revenue) }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/statistics.css') }}">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Hàm format tiền tệ
    function formatCurrency(value) {
        if (value >= 1000000) {
            return (value / 1000000).toFixed(1) + 'M';
        } else if (value >= 1000) {
            return (value / 1000).toFixed(0) + 'K';
        }
        return value.toString();
    }

    // Xử lý hiển thị/ẩn input date khi chọn period
    document.getElementById('period').addEventListener('change', function() {
        const customDateInputs = document.querySelectorAll('.custom-date');
        if (this.value === 'custom') {
            customDateInputs.forEach(input => input.classList.remove('d-none'));
        } else {
            customDateInputs.forEach(input => input.classList.add('d-none'));
        }
    });

    // Biểu đồ doanh thu
    const revenueData = @json($completeRevenueData);
    const period = '{{ $period }}';


    // Tính step size cho trục y dựa trên dữ liệu
    const maxRevenue = Math.max(...revenueData.map(item => item.revenue));
    let stepSize;


    if (maxRevenue >= 1000000) {
        // Nếu doanh thu > 1M, step size theo đơn vị triệu
        stepSize = Math.ceil(maxRevenue / 1000000 / 10) * 1000000;
    } else if (maxRevenue >= 1000) {
        // Nếu doanh thu > 1K, step size theo đơn vị nghìn
        stepSize = Math.ceil(maxRevenue / 1000 / 10) * 1000;
    } else {
        stepSize = Math.ceil(maxRevenue / 10);
    }

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revenueData.map(item => item.label),
            datasets: [{
                label: 'Doanh thu',
                data: revenueData.map(item => item.revenue),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Số đơn',
                data: revenueData.map(item => item.orders),
                type: 'line',
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                yAxisID: 'orders'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.yAxisID === 'orders') {
                                return `Số đơn: ${context.raw}`;
                            }
                            // Format doanh thu trong tooltip
                            const value = context.raw;
                            if (value >= 1000000) {
                                return `Doanh thu: ${(value / 1000000).toFixed(1)} triệu`;
                            } else if (value >= 1000) {
                                return `Doanh thu: ${(value / 1000).toFixed(0)}K`;
                            }
                            return `Doanh thu: ${value}đ`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    ticks: {
                        stepSize: stepSize,
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    },
                    grid: {
                        drawOnChartArea: true
                    }
                },
                orders: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    // Biểu đồ trạng thái đơn hàng
    const orderStatusData = @json($orderStatus);
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'pie',
        data: {
            labels: orderStatusData.map(item => item.Status),
            datasets: [{
                data: orderStatusData.map(item => item.total),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw;
                        }
                    }
                }
            }
        }
    });
</script>
@stop
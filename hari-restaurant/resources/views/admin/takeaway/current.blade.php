@extends('adminlte::page')

@section('title', 'Current Takeaway Orders')

@section('content_header')
    <h1>Current Takeaway Orders</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Status summary boxes -->
    <div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $statusCounts['Pending'] ?? 0 }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $statusCounts['Confirmed'] ?? 0 }}</h3>
                    <p>Confirmed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $statusCounts['In Preparation'] ?? 0 }}</h3>
                    <p>In Preparation</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $statusCounts['Out for Delivery'] ?? 0 }}</h3>
                    <p>Out for Delivery</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and filter form -->
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">Search & Filter</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.takeaway-orders.search') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Order Code</label>
                            <input type="text" name="order_code" class="form-control" value="{{ request('order_code') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ request('customer_name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ request('phone') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="In Preparation" {{ request('status') == 'In Preparation' ? 'selected' : '' }}>In Preparation</option>
                                <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.takeaway-orders.current') }}" class="btn btn-default">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders list -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Current Takeaway Orders</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Estimated Delivery</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->order_code }}</td>
                                <td>
                                    <strong>{{ $order->customer_name }}</strong><br>
                                    {{ $order->phone }}<br>
                                    <small>{{ Str::limit($order->address, 30) }}</small>
                                </td>
                                <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                <td>
                                    @if($order->status == 'Pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($order->status == 'Confirmed')
                                        <span class="badge bg-info">Confirmed</span>
                                    @elseif($order->status == 'In Preparation')
                                        <span class="badge bg-primary">In Preparation</span>
                                    @elseif($order->status == 'Out for Delivery')
                                        <span class="badge bg-success">Out for Delivery</span>
                                    @elseif($order->status == 'Delivered')
                                        <span class="badge bg-success">Delivered</span>
                                    @elseif($order->status == 'Cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->payment_method }}<br>
                                    @if($order->payment_status == 'Pending')
                                        <span class="badge bg-warning">Payment Pending</span>
                                    @elseif($order->payment_status == 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($order->payment_status == 'Refunded')
                                        <span class="badge bg-danger">Refunded</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($order->estimated_delivery_time)
                                        {{ $order->estimated_delivery_time->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.takeaway-orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No current orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .icon i {
            font-size: 50px;
            top: 20px;
            right: 20px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Highlight the row when clicked
            $('table tbody tr').click(function() {
                window.location = $(this).find('a').attr('href');
            }).css('cursor', 'pointer');
        });
    </script>
@stop 
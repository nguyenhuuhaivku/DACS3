@extends('adminlte::page')

@section('title', 'Completed Takeaway Orders')

@section('content_header')
    <h1>Completed Takeaway Orders</h1>
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
                <input type="hidden" name="completed" value="1">
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
                                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                            <a href="{{ route('admin.takeaway-orders.completed') }}" class="btn btn-default">
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
            <h3 class="card-title">Completed Takeaway Orders</h3>
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
                            <th>Order Date</th>
                            <th>Completed Date</th>
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
                                    @if($order->status == 'Delivered')
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
                                    @if($order->status == 'Delivered' && $order->delivery_time)
                                        {{ $order->delivery_time->format('d/m/Y H:i') }}
                                    @elseif($order->status == 'Cancelled')
                                        {{ $order->updated_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Not available</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.takeaway-orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.takeaway-orders.export', $order->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Export
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No completed orders found</td>
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

@section('js')
    <script>
        $(document).ready(function() {
            // Highlight the row when clicked for viewing
            $('table tbody tr').click(function(e) {
                if (!$(e.target).is('a, button, i')) {
                    window.location = $(this).find('a:first').attr('href');
                }
            }).css('cursor', 'pointer');
        });
    </script>
@stop 
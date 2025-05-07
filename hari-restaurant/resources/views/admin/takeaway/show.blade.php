@extends('adminlte::page')

@section('title', 'Takeaway Order Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Takeaway Order #{{ $order->order_code }}</h1>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.takeaway-orders.export', $order->id) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-file-pdf"></i> Export
            </a>
        </div>
    </div>
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
            @if(session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Order Info and Status Update -->
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Information</h3>
                    <div class="card-tools">
                        <span class="badge 
                            @if($order->status == 'Pending') badge-warning
                            @elseif($order->status == 'Confirmed') badge-info
                            @elseif($order->status == 'In Preparation') badge-primary
                            @elseif($order->status == 'Out for Delivery') badge-success
                            @elseif($order->status == 'Delivered') badge-success
                            @elseif($order->status == 'Cancelled') badge-danger
                            @endif
                        ">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl>
                                <dt>Order Code:</dt>
                                <dd>{{ $order->order_code }}</dd>
                                
                                <dt>Customer Name:</dt>
                                <dd>{{ $order->customer_name }}</dd>
                                
                                <dt>Phone Number:</dt>
                                <dd>{{ $order->phone }}</dd>
                                
                                <dt>Delivery Address:</dt>
                                <dd>{{ $order->address }}</dd>
                                
                                @if($order->note)
                                    <dt>Note:</dt>
                                    <dd>{{ $order->note }}</dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt>Order Date:</dt>
                                <dd>{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                                
                                <dt>Payment Method:</dt>
                                <dd>{{ $order->payment_method }}</dd>
                                
                                <dt>Payment Status:</dt>
                                <dd>
                                    @if($order->payment_status == 'Pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($order->payment_status == 'Paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($order->payment_status == 'Refunded')
                                        <span class="badge badge-danger">Refunded</span>
                                    @endif
                                </dd>
                                
                                <dt>Estimated Delivery Time:</dt>
                                <dd>
                                    @if($order->estimated_delivery_time)
                                        {{ $order->estimated_delivery_time->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </dd>
                                
                                @if($order->delivery_time)
                                    <dt>Actual Delivery Time:</dt>
                                    <dd>{{ $order->delivery_time->format('d/m/Y H:i') }}</dd>
                                @endif
                                
                                <dt>Total Amount:</dt>
                                <dd><strong>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</strong></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Items</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->menuItem && $item->menuItem->ImageURL)
                                                <img src="{{ asset($item->menuItem->ImageURL) }}" alt="{{ $item->menuItem->ItemName }}" class="img-thumbnail mr-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->menuItem ? $item->menuItem->ItemName : "Menu Item #$item->item_id" }}</strong>
                                                @if($item->notes)
                                                    <br><small class="text-muted">{{ $item->notes }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VNĐ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Update Status Form -->
            @if(!in_array($order->status, ['Delivered', 'Cancelled']))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Order Status</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.takeaway-orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">New Status</label>
                                        <select name="status" id="status" class="form-control">
                                            @foreach($statuses as $status => $label)
                                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="comment">Comment (Optional)</label>
                                        <input type="text" name="comment" id="comment" class="form-control" placeholder="Add a comment about this status update">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Update Estimated Delivery Time -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Estimated Delivery Time</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.takeaway-orders.update-delivery-time', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estimated_delivery_time">Estimated Delivery Time</label>
                                        <input type="datetime-local" name="estimated_delivery_time" id="estimated_delivery_time" class="form-control" 
                                            value="{{ $order->estimated_delivery_time ? $order->estimated_delivery_time->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-clock"></i> Update Delivery Time
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Customer Info and Tracking Timeline -->
        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <div class="card-body">
                    @if($order->user)
                        <div class="text-center mb-3">
                            <i class="fas fa-user-circle fa-4x text-info"></i>
                            <h5 class="mt-2">{{ $order->user->name }}</h5>
                            <p class="text-muted">{{ $order->user->email }}</p>
                        </div>
                        <hr>
                        <dl>
                            <dt>User ID:</dt>
                            <dd>{{ $order->user->id }}</dd>
                            
                            <dt>Registered on:</dt>
                            <dd>{{ $order->user->created_at->format('d/m/Y') }}</dd>
                            
                            <dt>Total Orders:</dt>
                            <dd>{{ $order->user->orders()->count() }}</dd>
                        </dl>
                        <a href="{{ route('admin.customers.show', $order->user->id) }}" class="btn btn-sm btn-block btn-info">
                            <i class="fas fa-eye"></i> View Customer Profile
                        </a>
                    @else
                        <div class="text-center">
                            <i class="fas fa-user fa-4x text-secondary"></i>
                            <h5 class="mt-2">{{ $order->customer_name }}</h5>
                            <p class="text-muted">Guest Customer</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Tracking Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Timeline</h3>
                </div>
                <div class="card-body p-0">
                    <div class="timeline timeline-inverse p-3">
                        <!-- Order placed event -->
                        <div class="time-label">
                            <span class="bg-success">
                                {{ $order->created_at->format('d M Y') }}
                            </span>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $order->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">Order Placed</h3>
                                <div class="timeline-body">
                                    Customer placed a new takeaway order with {{ $order->items->count() }} items.
                                </div>
                            </div>
                        </div>

                        <!-- Status change events -->
                        @foreach($timeline as $event)
                            <div>
                                @if($event->status == 'Confirmed')
                                    <i class="fas fa-check bg-info"></i>
                                @elseif($event->status == 'In Preparation')
                                    <i class="fas fa-utensils bg-warning"></i>
                                @elseif($event->status == 'Out for Delivery')
                                    <i class="fas fa-truck bg-primary"></i>
                                @elseif($event->status == 'Delivered')
                                    <i class="fas fa-check-double bg-success"></i>
                                @elseif($event->status == 'Cancelled')
                                    <i class="fas fa-times bg-danger"></i>
                                @else
                                    <i class="fas fa-circle bg-secondary"></i>
                                @endif
                                
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $event->created_at->format('H:i') }}
                                    </span>
                                    <h3 class="timeline-header">Status Changed: <strong>{{ $event->status }}</strong></h3>
                                    
                                    @if($event->comment)
                                        <div class="timeline-body">
                                            {{ $event->comment }}
                                        </div>
                                    @endif
                                    
                                    <div class="timeline-footer">
                                        <small class="text-muted">
                                            Updated by: 
                                            @if($event->admin)
                                                {{ $event->admin->name }}
                                            @else
                                                System
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .timeline {
            margin: 0;
            padding: 0;
            position: relative;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            left: 31px;
            margin: 0;
            border-radius: 2px;
        }
        .timeline > div {
            margin-right: 10px;
            margin-bottom: 15px;
            position: relative;
        }
        .time-label {
            margin-bottom: 20px;
        }
        .time-label > span {
            font-weight: 600;
            padding: 5px 10px;
            display: inline-block;
            border-radius: 4px;
            color: #fff;
        }
        .timeline-item {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: 3px;
            margin-left: 60px;
            margin-right: 15px;
            margin-top: 0;
            background: #fff;
            color: #444;
            padding: 10px;
            position: relative;
        }
        .timeline > div > i {
            width: 30px;
            height: 30px;
            font-size: 15px;
            line-height: 30px;
            position: absolute;
            color: #fff;
            background: #6c757d;
            border-radius: 50%;
            text-align: center;
            left: 18px;
            top: 0;
        }
    </style>
@stop 
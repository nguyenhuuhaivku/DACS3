@extends('adminlte::page')
@section('title', 'Quản lý khách hàng')
@section('content_header')
<h1>Quản lý khách hàng</h1>
@stop
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách khách hàng</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Khách Hàng</th>
                    <th>Email</th>
                    <th>Số lần đặt bàn</th>
                    <th>Tổng chi tiêu</th>
                    <th>Lần đặt cuối</th>
                    <th>Ngày tham gia</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->total_reservations }}</td>
                    <td>{{ number_format($customer->total_spent ?? 0) }}đ</td>
                    <td>{{ $customer->last_reservation ? date('d/m/Y', strtotime($customer->last_reservation)) : 'Chưa đặt' }}</td>
                    <td>{{ date('d/m/Y', strtotime($customer->created_at)) }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer->id) }}"
                            class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Chi tiết
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@stop


@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop


@section('js')
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@stop
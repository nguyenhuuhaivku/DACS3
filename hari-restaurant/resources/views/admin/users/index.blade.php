@extends('layouts.app')

@section('title')
    Quản lý Người Dùng
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0 font-weight-bold text-primary">Quản Lý Người Dùng</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm Người Dùng
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Chức Năng</th>
                            <th class="text-center">Hoạt Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        border: none;
    }
    
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .table th {
        vertical-align: middle;
    }
    
    .btn {
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
</style>
@endsection

@extends('layouts.app')

@section('title')
    Quản lý Danh Mục
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0 font-weight-bold text-primary">Quản Lý Danh Mục</h3>
            <a href="{{ route('admin.menu.categories.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm Danh Mục
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tên Danh Mục</th>
                            <th>Mô Tả</th>
                            <th class="text-center">Hoạt Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $category)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $category->CategoryName }}</td>
                                <td>{{ $category->Description }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.menu.categories.edit', $category->CategoryID) }}" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.menu.categories.destroy', $category->CategoryID) }}" 
                                          method="POST" 
                                          class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
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

@extends('layouts.app')

@section('title')
    Quản lý Món Ăn
@endsection

@section('content')
    <div class="container-fluid px-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">Quản Lý Món Ăn</h3>
                <a href="{{ route('admin.menu.items.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm Món Ăn
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center">Hình Ảnh</th>
                                <th>Tên Món</th>
                                <th>Giá</th>
                                <th>Danh Mục</th>
                                <th>Nhãn</th>
                                <th class="text-center">Trạng Thái</th>
                                <th class="text-center">Hoạt Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td class="text-center" style="width: 120px;">
                                        @if ($item->ImageURL)
                                            <img src="{{ asset($item->ImageURL) }}" alt="{{ $item->ItemName }}"
                                                class="menu-item-image">
                                        @else
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="align-middle">{{ $item->ItemName }}</td>
                                    <td class="align-middle">{{ number_format($item->Price, 0, ',', '.') }} VND</td>
                                    <td class="align-middle">{{ $item->category->CategoryName ?? 'N/A' }}</td>
                                    <td class="align-middle">
                                        <span class="badge {{ $item->statusBadgeClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="status-badge {{ $item->Available ? 'available' : 'unavailable' }}">
                                            {{ $item->Available ? 'Còn hàng' : 'Hết hàng' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.menu.items.edit', $item->ItemID) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.menu.items.destroy', $item->ItemID) }}"
                                            method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa món ăn này?')">
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
            background-color: rgba(0, 0, 0, .075);
        }

        /* Styling cho hình ảnh món ăn */
        .menu-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .no-image {
            width: 100px;
            height: 100px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 2rem;
        }

        /* Styling cho badge trạng thái */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.available {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-badge.unavailable {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .menu-item-image,
            .no-image {
                width: 80px;
                height: 80px;
            }

            .table td {
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

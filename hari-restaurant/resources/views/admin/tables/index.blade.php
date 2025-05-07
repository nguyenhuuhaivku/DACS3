@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Sơ đồ bàn</h1>

    <!-- Dropdown lọc vị trí -->
    <div class="row mb-4">
        <div class="col-md-4">
            <form action="{{ route('admin.tables.index') }}" method="GET">
                <label for="location" class="form-label">Chọn khu vực</label>
                <select name="location" id="location" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả khu vực --</option>
                    @foreach ($locations as $location)
                    <option value="{{ $location->Location }}"
                        {{ $selectedLocation == $location->Location ? 'selected' : '' }}>
                        {{ $location->Location }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="col-md-8 text-end">
            <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm bàn mới
            </a>
        </div>
    </div>

    <!-- Chú thích trạng thái -->
    <div class="table-status-legend mb-4">
        <span class="status-item status-empty">Trống</span>
        <span class="status-item status-in-use">Đang sử dụng</span>
        <span class="status-item status-maintenance">Bảo trì</span>
    </div>

    <!-- Sơ đồ bàn -->
    <div class="table-grid">
        @foreach ($tables as $table)
        <div class="table-item {{ strtolower(str_replace(' ', '-', $table->Status)) }}">
            <div class="table-content">
                <div class="table-number">{{ $table->TableNumber }}</div>
                <div class="table-info">
                    <span class="seats"><i class="fas fa-chair"></i> {{ $table->Seats }}</span>
                    <span class="location"><i class="fas fa-map-marker-alt"></i> {{ $table->Location }}</span>
                </div>
                <div class="status">
                    @php
                    $statusColors = [
                    'Trống' => '#28a745',
                    'Đang sử dụng' => '#dc3545',
                    'Bảo trì' => '#ffc107',
                    ];
                    @endphp
                    <span class="status-dot"
                        style="background-color: {{ $statusColors[$table->Status] ?? '#000' }}; display: inline-block; width: 15px; height: 15px; border-radius: 50%;">
                    </span>
                </div>

                <div class="table-actions">
                    <a href="{{ route('admin.tables.edit', $table->TableID) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tables.destroy', $table->TableID) }}" method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa bàn này?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<style>
    /* Chú thích trạng thái */
    .table-status-legend {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .status-item {
        display: inline-flex;
        align-items: center;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9em;
    }

    .status-item::before {
        content: '';
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .status-empty::before {
        background-color: #28a745;
    }

    .status-in-use::before {
        background-color: #dc3545;
    }

    .status-maintenance::before {
        background-color: #ffc107;
    }

    /* Lưới bàn */
    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .table-item {
        border-radius: 10px;
        padding: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Màu sắc theo trạng thái */
    .trong {
        background-color: #d4edda;
        border: 2px solid #28a745;
    }

    .dang-su-dung {
        background-color: #f8d7da;
        border: 2px solid #dc3545;
    }

    .bao-tri {
        background-color: #fff3cd;
        border: 2px solid #ffc107;
    }

    .table-content {
        text-align: center;
    }

    .table-number {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .table-info {
        margin-bottom: 15px;
    }

    .table-info span {
        display: block;
        margin: 5px 0;
        font-size: 0.9em;
    }

    .table-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .table-actions .btn {
        padding: 5px 10px;
    }

    /* Hiệu ứng hover */
    .table-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    /* Thông báo không có bàn */
    .no-tables {
        grid-column: 1 / -1;
        text-align: center;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: 10px;
        font-style: italic;
        color: #6c757d;
    }
</style>

<!-- Thêm Font Awesome nếu chưa có -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
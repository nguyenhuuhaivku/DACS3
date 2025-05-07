@extends('layouts.app')

@section('title')
    Sửa Thông Tin Bàn
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0 font-weight-bold text-primary">Sửa Thông Tin Bàn {{ $table->TableNumber }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tables.update', $table->TableID) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="TableNumber" class="form-label fw-bold">Số Bàn:</label>
                            <input type="text" name="TableNumber" 
                                   class="form-control @error('TableNumber') is-invalid @enderror"
                                   value="{{ old('TableNumber', $table->TableNumber) }}" required>
                            @error('TableNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="Seats" class="form-label fw-bold">Số Ghế:</label>
                            <input type="number" name="Seats" 
                                   class="form-control @error('Seats') is-invalid @enderror"
                                   value="{{ old('Seats', $table->Seats) }}" required min="1">
                            @error('Seats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="Location" class="form-label fw-bold">Vị Trí:</label>
                            <select name="Location" class="form-control @error('Location') is-invalid @enderror" required>
                                <option value="Trong nhà" {{ $table->Location == 'Trong nhà' ? 'selected' : '' }}>Trong nhà</option>
                                <option value="Ngoài sân" {{ $table->Location == 'Ngoài sân' ? 'selected' : '' }}>Ngoài sân</option>
                                <option value="VIP" {{ $table->Location == 'VIP' ? 'selected' : '' }}>VIP</option>
                            </select>
                            @error('Location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="Status" class="form-label fw-bold">Trạng Thái:</label>
                            <select name="Status" class="form-control @error('Status') is-invalid @enderror" required>
                                <option value="Trống" {{ $table->Status == 'Trống' ? 'selected' : '' }}>Trống</option>
                                <option value="Đang sử dụng" {{ $table->Status == 'Đang sử dụng' ? 'selected' : '' }}>Đang sử dụng</option>
                                <option value="Bảo trì" {{ $table->Status == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                            @error('Status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Trở Về
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
    
    .form-control {
        padding: 0.75rem;
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: border-color 0.2s;
    }
    
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }
    
    .form-label {
        color: #4e73df;
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@extends('layouts.app')

@section('title')
    Sửa Danh Mục
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0 font-weight-bold text-primary">Sửa Danh Mục</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.menu.categories.update', $category->CategoryID) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="CategoryName" class="form-label fw-bold">Tên Danh Mục:</label>
                            <input type="text" name="CategoryName" class="form-control @error('CategoryName') is-invalid @enderror"
                                   value="{{ old('CategoryName', $category->CategoryName) }}" required>
                            @error('CategoryName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="Description" class="form-label fw-bold">Mô Tả:</label>
                            <textarea name="Description" class="form-control @error('Description') is-invalid @enderror" 
                                      rows="4">{{ old('Description', $category->Description) }}</textarea>
                            @error('Description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <a href="{{ route('admin.menu.categories.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Trở Về
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập Nhật
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

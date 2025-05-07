@extends('layouts.app')

@section('title')
    Thêm Người Dùng
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0 font-weight-bold text-primary">Thêm Người Dùng</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label fw-bold">Tên:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   required value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label fw-bold">Email:</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   required value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label fw-bold">Mật Khẩu:</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">Xác Nhận Mật Khẩu:</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="roles" class="form-label fw-bold">Chức Năng:</label>
                            <select name="roles" class="form-control @error('roles') is-invalid @enderror" required>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
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

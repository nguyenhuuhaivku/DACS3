@extends('layouts.app')

@section('title')
    Sửa Người Dùng
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0 font-weight-bold text-primary">Sửa Người Dùng</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label fw-bold">Tên:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label fw-bold">Email:</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="roles" class="form-label fw-bold">Chức Năng:</label>
                            <select name="roles" class="form-control @error('roles') is-invalid @enderror" required>
                                <option value="User" {{ $user->roles == 'User' ? 'selected' : '' }}>User</option>
                                <option value="Admin" {{ $user->roles == 'Admin' ? 'selected' : '' }}>Admin</option>
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
                                <i class="fas fa-save"></i> Cập Nhật
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

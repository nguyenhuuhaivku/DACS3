@extends('layouts.app')

@section('title')
    Thêm Món Ăn
@endsection

@section('content')
    <div class="container-fluid px-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">Thêm Món Ăn</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.menu.items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="CategoryID" class="form-label fw-bold">Danh Mục:</label>
                                <select name="CategoryID" class="form-control @error('CategoryID') is-invalid @enderror"
                                    required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->CategoryID }}"
                                            {{ old('CategoryID') == $category->CategoryID ? 'selected' : '' }}>
                                            {{ $category->CategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('CategoryID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="ItemName" class="form-label fw-bold">Tên Món Ăn:</label>
                                <input type="text" name="ItemName"
                                    class="form-control @error('ItemName') is-invalid @enderror"
                                    value="{{ old('ItemName') }}" required>
                                @error('ItemName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="Price" class="form-label fw-bold">Giá:</label>
                                <input type="number" name="Price"
                                    class="form-control @error('Price') is-invalid @enderror" value="{{ old('Price') }}"
                                    required>
                                @error('Price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="status" class="form-label fw-bold">Nhãn:</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    @foreach (App\Models\admin\MenuItem::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="Description" class="form-label fw-bold">Mô Tả:</label>
                                <textarea name="Description" class="form-control @error('Description') is-invalid @enderror" rows="4">{{ old('Description') }}</textarea>
                                @error('Description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="Image" class="form-label fw-bold">Hình Ảnh:</label>
                                <input type="file" name="Image"
                                    class="form-control @error('Image') is-invalid @enderror">
                                @error('Image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="Available" class="form-label fw-bold">Trạng Thái:</label>
                                <select name="Available" class="form-control @error('Available') is-invalid @enderror">
                                    <option value="1" {{ old('Available') == 1 ? 'selected' : '' }}>Còn hàng</option>
                                    <option value="0" {{ old('Available') == 0 ? 'selected' : '' }}>Hết hàng</option>
                                </select>
                                @error('Available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center">
                                <a href="{{ route('admin.menu.items.index') }}" class="btn btn-secondary me-2">
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

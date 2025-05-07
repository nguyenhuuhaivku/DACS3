@extends('layouts.app')

@section('title')
    Thêm Bàn Mới
@endsection

@section('content')
    <div class="container-fluid px-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">Thêm Bàn Mới</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tables.store') }}" method="POST">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="TableNumber" class="form-label fw-bold">Số Bàn:</label>
                                <input type="text" name="TableNumber"
                                    class="form-control @error('TableNumber') is-invalid @enderror"
                                    value="{{ old('TableNumber') }}" required>
                                @error('TableNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="Seats" class="form-label fw-bold">Số Ghế:</label>
                                <input type="number" name="Seats"
                                    class="form-control @error('Seats') is-invalid @enderror" value="{{ old('Seats') }}"
                                    required min="1">
                                @error('Seats')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="Location" class="form-label fw-bold">Vị Trí:</label>
                                <select name="Location" class="form-control @error('Location') is-invalid @enderror"
                                    required>
                                    <option value="">Chọn vị trí</option>
                                    <option value="Trong nhà" {{ old('Location') == 'Trong nhà' ? 'selected' : '' }}>Trong
                                        nhà</option>
                                    <option value="Ngoài sân" {{ old('Location') == 'Ngoài sân' ? 'selected' : '' }}>Ngoài
                                        sân</option>
                                    <option value="VIP" {{ old('Location') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                </select>
                                @error('Location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="Status" class="form-label fw-bold">Trạng Thái:</label>
                                <select name="Status" class="form-control @error('Status') is-invalid @enderror" required>
                                    <option value="Trống" {{ old('Status') == 'Trống' ? 'selected' : '' }}>Trống</option>
                                    <option value="Đang sử dụng" {{ old('Status') == 'Đang sử dụng' ? 'selected' : '' }}>
                                        Đang sử dụng</option>
                                    <option value="Bảo trì" {{ old('Status') == 'Bảo trì' ? 'selected' : '' }}>Bảo trì
                                    </option>
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

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endsection

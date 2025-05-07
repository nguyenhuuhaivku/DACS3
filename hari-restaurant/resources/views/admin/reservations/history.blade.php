@extends('adminlte::page')




@section('title', 'Lịch sử đặt bàn')




@section('content_header')
    <h1>Lịch sử đặt bàn</h1>
@stop




@section('content')
    <div class="card">
        <div class="card-body">
            <form id="filterForm" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Thời gian</label>
                            <select class="form-control" name="period" id="period">
                                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Tuần này</option>
                                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Tháng này
                                </option>
                                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Năm này</option>
                                <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Tùy chọn
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 custom-date {{ request('period') == 'custom' ? '' : 'd-none' }}">
                        <div class="form-group">
                            <label>Từ ngày</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>
                    </div>

                    <div class="col-md-3 custom-date {{ request('period') == 'custom' ? '' : 'd-none' }}">
                        <div class="form-group">
                            <label>Đến ngày</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select class="form-control" name="status">
                                <option value="">Tất cả</option>
                                <option value="Đã hoàn tất" {{ request('status') == 'Đã hoàn tất' ? 'selected' : '' }}>Đã
                                    hoàn tất</option>
                                <option value="Đã hủy" {{ request('status') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="buttons-container">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Lọc
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                                <i class="fas fa-sync"></i> Đặt lại
                            </button>
                        </div>
                    </div>
                </div>
            </form>


            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Mã đặt bàn</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Ngày đặt</th>
                        <th>Bàn</th>
                        <th>Số khách</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th>Thời gian sử dụng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>#{!! $reservation->ReservationCode !!}</td>
                            <td>{!! $reservation->FullName !!}</td>
                            <td>{!! $reservation->Phone !!}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('d/m/Y') }}</td>
                            <td>
                                @if ($reservation->table)
                                    Bàn {!! $reservation->table->TableNumber !!}
                                @else
                                    --
                                @endif
                            </td>
                            <td>{!! $reservation->GuestCount !!}</td>
                            <td>
                                @if ($reservation->CheckInTime)
                                    {{ \Carbon\Carbon::parse($reservation->CheckInTime)->format('H:i') }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if ($reservation->CheckOutTime)
                                    {{ \Carbon\Carbon::parse($reservation->CheckOutTime)->format('H:i') }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if ($reservation->CheckInTime && $reservation->CheckOutTime)
                                    @php
                                        $checkIn = \Carbon\Carbon::parse($reservation->CheckInTime);
                                        $checkOut = \Carbon\Carbon::parse($reservation->CheckOutTime);
                                        $duration = $checkOut->diffInMinutes($checkIn);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    {{ $hours }}h{{ $minutes > 0 ? $minutes . 'p' : '' }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $reservation->Status === 'Đã hoàn tất' ? 'success' : 'danger' }}">
                                    {{ $reservation->Status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop




@section('css')
    <style>
        .table th {
            background-color: #f4f6f9;
            text-align: center;
            vertical-align: middle;
        }




        .table td {
            text-align: center;
            vertical-align: middle;
        }




        .pagination {
            margin-bottom: 0;
        }




        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }




        .page-link {
            color: #007bff;
        }




        .page-link:hover {
            color: #0056b3;
        }


        #filterForm {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }


        #filterForm .row {
            display: flex;
            align-items: flex-end;
            margin: 0 -10px;
        }


        #filterForm .col-md-3 {
            padding: 0 10px;
            margin-bottom: 0;
        }


        #filterForm .form-group {
            margin-bottom: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }


        #filterForm label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }


        #filterForm .form-control {
            height: 38px;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }


        #filterForm .buttons-container {
            display: flex;
            gap: 10px;
            height: 38px;
            align-items: center;
        }


        #filterForm .btn {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            font-weight: 500;
            white-space: nowrap;
        }


        #filterForm .btn i {
            margin-right: 6px;
        }


        @media (max-width: 768px) {
            #filterForm .row {
                flex-direction: column;
            }


            #filterForm .col-md-3 {
                width: 100%;
                margin-bottom: 15px;
            }


            #filterForm .buttons-container {
                justify-content: flex-start;
            }
        }
    </style>
@stop


@section('js')
    <script>
        document.getElementById('period').addEventListener('change', function() {
            const customDateInputs = document.querySelectorAll('.custom-date');
            customDateInputs.forEach(input => {
                input.classList.toggle('d-none', this.value !== 'custom');
            });
        });


        function resetFilter() {
            window.location.href = '{{ route('admin.reservations.history') }}';
        }
    </script>
@stop

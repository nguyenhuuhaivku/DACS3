@extends('adminlte::page')








@section('title', 'Hóa đơn đã hoàn tất')








@section('content_header')
    <h1>Hóa đơn đã hoàn tất</h1>
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
                        <th>Mã HĐ</th>
                        <th>Khách hàng</th>
                        <th>Ngày</th>
                        <th>Bàn</th>
                        <th>Số khách</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền</th>
                        <th>Phương thức</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>#{!! $invoice->PaymentCode !!}</td>
                            <td>{!! $invoice->reservation->FullName !!}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->reservation->ReservationDate)->format('d/m/Y') }}</td>
                            <td>
                                @if ($invoice->reservation->table)
                                    Bàn {!! $invoice->reservation->table->TableNumber !!}
                                @else
                                    --
                                @endif
                            </td>
                            <td>{!! $invoice->reservation->GuestCount !!}</td>
                            <td>
                                @if ($invoice->reservation->CheckInTime)
                                    {{ \Carbon\Carbon::parse($invoice->reservation->CheckInTime)->format('H:i') }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if ($invoice->reservation->CheckOutTime)
                                    {{ \Carbon\Carbon::parse($invoice->reservation->CheckOutTime)->format('H:i') }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if ($invoice->reservation->CheckInTime && $invoice->reservation->CheckOutTime)
                                    @php
                                        $checkIn = \Carbon\Carbon::parse($invoice->reservation->CheckInTime);
                                        $checkOut = \Carbon\Carbon::parse($invoice->reservation->CheckOutTime);
                                        $duration = $checkOut->diffInMinutes($checkIn);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    {{ $hours }}h{{ $minutes > 0 ? $minutes . 'p' : '' }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>{!! number_format($invoice->Amount) !!}đ</td>
                            <td>{!! $invoice->PaymentMethod !!}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info"
                                    onclick="previewInvoice({{ $invoice->PaymentID }})">
                                    <i class="fas fa-file-invoice"></i> Xuất hóa đơn
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Không có hóa đơn nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>








            <div class="mt-3 d-flex justify-content-center">
                {{ $invoices->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>








    <!-- Modal Preview -->
    <div class="modal fade" id="invoicePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xem hóa đơn</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="invoicePreviewContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="printInvoice()">
                        <i class="fas fa-print"></i> In
                    </button>
                    <button type="button" class="btn btn-success" onclick="downloadPDF()">
                        <i class="fas fa-download"></i> Tải PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop








@section('css')
    <style>
        /* CSS cho form lọc */
        #filterForm {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }


        /* CSS cho hàng chứa các trường lọc */
        #filterForm .row {
            display: flex;
            align-items: flex-end;
            /* Căn chỉnh các cột theo bottom */
            margin: 0 -10px;
            /* Tạo khoảng cách giữa các cột */
        }


        /* CSS cho cột */
        #filterForm .col-md-3 {
            padding: 0 10px;
            margin-bottom: 0;
        }


        /* CSS cho form group */
        #filterForm .form-group {
            margin-bottom: 0;
            /* Bỏ margin bottom của form-group */
            height: 100%;
            /* Đảm bảo chiều cao đồng đều */
            display: flex;
            flex-direction: column;
        }


        /* CSS cho label */
        #filterForm label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }


        /* CSS cho input và select */
        #filterForm .form-control {
            height: 38px;
            /* Chiều cao cố định cho input/select */
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            width: 100%;
            transition: all 0.2s ease;
        }


        #filterForm .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }


        /* CSS cho container chứa buttons */
        #filterForm .buttons-container {
            display: flex;
            gap: 10px;
            height: 38px;
            /* Cùng chiều cao với input */
            align-items: center;
        }


        /* CSS cho buttons */
        #filterForm .btn {
            height: 38px;
            /* Cùng chiều cao với input */
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


        /* Responsive */
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


        /* CSS cho bảng */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }


        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }


        .table td {
            padding: 12px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }


        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05);
        }


        .table-bordered {
            border: 1px solid #dee2e6;
        }


        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }


        /* CSS cho modal */
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }


        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 15px 20px;
        }


        .modal-body {
            padding: 20px;
        }


        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
        }


        /* CSS cho các nút thao tác */
        .btn-group .btn {
            margin-right: 5px;
        }


        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
        }


        /* CSS cho thông báo */
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }


        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }


        /* CSS cho in ấn */
        @media print {
            .no-print {
                display: none !important;
            }


            .table {
                width: 100% !important;
                margin: 0 !important;
                border-collapse: collapse !important;
            }


            .table td,
            .table th {
                background-color: #fff !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
@stop








@section('js')
    <script>
        $(document).ready(function() {
            // Xử lý hiển thị/ẩn trường tùy chọn ngày
            $('#period').change(function() {
                if ($(this).val() === 'custom') {
                    $('.custom-date').removeClass('d-none');
                } else {
                    $('.custom-date').addClass('d-none');
                }
            });
        });


        function resetFilter() {
            window.location.href = '{{ route('admin.invoices.completed') }}';
        }


        function previewInvoice(invoiceId) {
            $('#invoicePreviewContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
            $('#invoicePreviewModal').modal('show');




            $.ajax({
                url: `/admin/invoices/${invoiceId}/preview`,
                method: 'GET',
                success: function(response) {
                    $('#invoicePreviewContent').html(response);
                    $('#invoicePreviewModal').data('invoice-id', invoiceId);
                    adjustInvoiceStyles();
                },
                error: function(xhr) {
                    console.error('Preview error:', xhr);
                    $('#invoicePreviewContent').html(
                        '<div class="alert alert-danger">Có lỗi xảy ra khi tải hóa đơn</div>');
                }
            });
        }




        function adjustInvoiceStyles() {
            const container = $('#invoicePreviewContent').find('.invoice-container');
            container.css({
                'width': '100%',
                'max-width': '800px',
                'margin': '0 auto',
                'padding': '20px'
            });
        }




        function printInvoice() {
            if ($('#invoicePreviewContent').children().length > 0) {
                // Đảm bảo styles được áp dụng trước khi in
                adjustInvoiceStyles();
                setTimeout(() => {
                    window.print();
                }, 100);
            } else {
                alert('Vui lòng đợi nội dung hóa đơn được tải xong');
            }
        }




        function downloadPDF() {
            const invoiceId = $('#invoicePreviewModal').data('invoice-id');
            if (invoiceId) {
                window.location.href = `/admin/invoices/${invoiceId}/export`;
            }
        }




        // Điều chỉnh styles khi modal được hiển thị
        $('#invoicePreviewModal').on('shown.bs.modal', function() {
            adjustInvoiceStyles();
        });
    </script>
@stop

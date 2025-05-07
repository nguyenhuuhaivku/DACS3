@extends('adminlte::page')




@section('title', 'Hóa đơn hiện tại')




@section('content_header')
    <h1>Hóa đơn hiện tại</h1>
@stop




@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Form lọc -->
            <form id="filterForm" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Thời gian</label>
                            <select class="form-control" name="period" id="period">
                                <option value="">Tất cả</option>
                                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Tuần này</option>
                                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Tháng này
                                </option>
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
                            <label>Phương thức thanh toán</label>
                            <select class="form-control" name="payment_method">
                                <option value="">Tất cả</option>
                                <option value="Thanh toán tại nhà hàng"
                                    {{ request('payment_method') == 'Thanh toán tại nhà hàng' ? 'selected' : '' }}>
                                    Thanh toán tại nhà hàng
                                </option>
                                <option value="Chuyển khoản ngân hàng"
                                    {{ request('payment_method') == 'Chuyển khoản ngân hàng' ? 'selected' : '' }}>
                                    Chuyển khoản ngân hàng
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select class="form-control" name="status">
                                <option value="">Tất cả</option>
                                <option value="Chờ thanh toán"
                                    {{ request('status') == 'Chờ thanh toán' ? 'selected' : '' }}>
                                    Chờ thanh toán
                                </option>
                                <option value="Đã thanh toán" {{ request('status') == 'Đã thanh toán' ? 'selected' : '' }}>
                                    Đã thanh toán
                                </option>
                                <option value="Từ chối" {{ request('status') == 'Từ chối' ? 'selected' : '' }}>
                                    Từ chối
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


            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif




            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Mã hóa đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Phương thức</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>#{!! $invoice->PaymentCode !!}</td>
                            <td>{!! $invoice->reservation->FullName !!}</td>
                            <td>{!! date('d/m/Y H:i', strtotime($invoice->reservation->ReservationDate)) !!}</td>
                            <td>{!! number_format($invoice->Amount) !!}đ</td>
                            <td>{!! $invoice->PaymentMethod !!}</td>
                            <td>
                                @if ($invoice->PaymentMethod == 'Chuyển khoản ngân hàng')
                                    <div class="d-flex flex-column gap-2">
                                        <span
                                            class="badge bg-{{ $invoice->Status === 'Đã thanh toán' ? 'success' : ($invoice->Status === 'Từ chối' ? 'danger' : 'warning') }}">
                                            {{ $invoice->Status }}
                                        </span>
                                        @if ($invoice->PaymentProof)
                                            <button type="button" class="btn btn-sm btn-info mt-1"
                                                onclick="viewProof('{{ asset('storage/' . $invoice->PaymentProof) }}')">
                                                <i class="fas fa-image"></i> Xem biên lai
                                            </button>
                                            @if ($invoice->Status !== 'Đã thanh toán')
                                                <form action="{{ route('admin.invoices.confirm', $invoice->PaymentID) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success mt-1">
                                                        <i class="fas fa-check"></i> Xác nhận thanh toán
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.invoices.reject', $invoice->PaymentID) }}"
                                                    method="POST" data-payment-id="{{ $invoice->PaymentID }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" class="btn btn-sm btn-danger mt-1 reject-btn">
                                                        <i class="fas fa-times"></i> Từ chối đơn
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted">Chưa có biên lai</span>
                                        @endif
                                    </div>
                                @else
                                    <span
                                        class="badge bg-{{ $invoice->Status === 'Đã thanh toán' ? 'success' : ($invoice->Status === 'Từ chối' ? 'danger' : 'warning') }}">
                                        {{ $invoice->Status }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info"
                                        onclick="previewInvoice({{ $invoice->PaymentID }})">
                                        <i class="fas fa-file-invoice"></i> Xem hóa đơn
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có hóa đơn nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>




    <!--  xem biên lai -->
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Biên lai thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="proofImage" src="" alt="Biên lai" style="max-width: 100%;">
                </div>
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
                </div>
            </div>
        </div>
    </div>
@stop




@section('css')
    <style>
        .table td {
            text-align: center;
            vertical-align: middle;
        }




        @media print {
            body * {
                visibility: hidden;
            }




            .modal {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
            }




            #invoicePreviewContent,
            #invoicePreviewContent * {
                visibility: visible;
            }




            #invoicePreviewContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }




            .modal-header,
            .modal-footer {
                display: none !important;
            }
        }


        #filterForm {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }


        #filterForm .row {
            margin: 0 -10px;
        }


        #filterForm .col-md-3 {
            padding: 0 10px;
            margin-bottom: 15px;
        }


        #filterForm .form-group {
            margin-bottom: 0;
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
            align-items: flex-end;
            height: 100%;
        }


        #filterForm .btn {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            font-weight: 500;
        }


        #filterForm .btn i {
            margin-right: 6px;
        }


        @media (max-width: 768px) {
            #filterForm .buttons-container {
                margin-top: 15px;
            }
        }
    </style>
@stop




@section('js')
    <script>
        function viewProof(imageUrl) {
            document.getElementById('proofImage').src = imageUrl;
            new bootstrap.Modal(document.getElementById('proofModal')).show();
        }




        function previewInvoice(invoiceId) {
            $('#invoicePreviewContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
            $('#invoicePreviewModal').modal('show');




            $.get(`/admin/invoices/${invoiceId}/preview`, function(response) {
                $('#invoicePreviewContent').html(response);
                $('#invoicePreviewModal').data('invoice-id', invoiceId);
            });
        }




        function printInvoice() {
            window.print();
        }




        function downloadPDF() {
            const invoiceId = $('#invoicePreviewModal').data('invoice-id');
            window.location.href = `/admin/invoices/${invoiceId}/export`;
        }




        function handleReject(paymentId, buttonElement) {
            const row = $(buttonElement).closest('tr');
            const statusCell = row.find('td:nth-child(6)');
            const originalContent = statusCell.html();


            statusCell.html(`
                <span class="badge bg-danger">
                    Từ chối
                </span>
            `);


            const $buttonContainer = $(buttonElement).closest('.d-flex');
            $buttonContainer.find('button').prop('disabled', true);
            $(buttonElement).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');




            $.ajax({
                url: `/admin/invoices/${paymentId}/reject`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success('Đã từ chối đơn thanh toán thành công!');
                    $buttonContainer.remove();
                },
                error: function(xhr) {
                    statusCell.html(originalContent);
                    $buttonContainer.find('button').prop('disabled', false);
                    $(buttonElement).html('<i class="fas fa-times"></i> Từ chối đơn');
                    toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra khi từ chối đơn');
                }
            });
        }




        $(document).ready(function() {
            $('.reject-btn').on('click', function(e) {
                e.preventDefault();
                const paymentId = $(this).closest('form').data('payment-id');
                const button = this;


                if (confirm('Bạn có chắc chắn muốn từ chối đơn này?')) {
                    handleReject(paymentId, button);
                }
            });
        });


        document.getElementById('period').addEventListener('change', function() {
            const customDateInputs = document.querySelectorAll('.custom-date');
            customDateInputs.forEach(input => {
                input.classList.toggle('d-none', this.value !== 'custom');
            });
        });


        function resetFilter() {
            window.location.href = '{{ route('admin.invoices.current') }}';
        }
    </script>
@stop

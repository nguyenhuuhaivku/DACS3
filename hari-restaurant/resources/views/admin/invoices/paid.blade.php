@extends('adminlte::page')


@section('title', 'Hóa đơn đã thanh toán')


@section('content_header')
<h1>Hóa đơn đã thanh toán</h1>
@stop



@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Mã HĐ</th>
                    <th>Khách hàng</th>
                    <th>Ngày </th>
                    <th>Bàn</th>
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
                    <td colspan="10" class="text-center">Không có hóa đơn nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3 d-flex justify-content-center">
            {{ $invoices->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<!--  Preview -->
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
    .table th {
        background-color: #f4f6f9;
        text-align: center;
        vertical-align: middle;
    }


    .table td {
        text-align: center;
        vertical-align: middle;
    }


    /* Style cho phân trang */
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

    @media print {
        body * {
            visibility: hidden;
        }


        #invoicePreviewContent,
        #invoicePreviewContent * {
            visibility: visible;
        }


        #invoicePreviewContent {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>
@stop
@section('js')
<script>
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
</script>
@stop
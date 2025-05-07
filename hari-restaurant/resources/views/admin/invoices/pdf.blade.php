<!DOCTYPE html>
<html lang="vi">








<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $invoice->PaymentID }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }


        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }


        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border: 2px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: #fff;
        }


        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }


        .restaurant-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            text-transform: uppercase;
        }


        .restaurant-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }


        .invoice-title {
            text-align: center;
            margin: 30px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }


        .invoice-title h1 {
            font-size: 24px;
            margin: 0;
            color: #2c3e50;
        }


        .invoice-number {
            font-size: 16px;
            color: #666;
            margin-top: 5px;
        }


        .customer-info {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }


        .info-row {
            margin-bottom: 10px;
            display: flex;
        }


        .label {
            font-weight: bold;
            width: 150px;
            color: #2c3e50;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }


        th {
            background-color: #2c3e50;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }


        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }


        tr:nth-child(even) {
            background-color: #f8f9fa;
        }


        .text-right {
            text-align: right;
        }


        .total-section {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }


        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }


        .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            padding: 10px 0;
            margin-top: 10px;
            border-top: 1px solid #ddd;
        }


        .payment-status {
            margin: 30px 0;
            text-align: center;
        }


        .status-box {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }


        .paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }


        .pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }


        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }


        .signature-box {
            width: 45%;
            text-align: center;
        }


        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
            color: #2c3e50;
        }


        .signature-line {
            border-top: 1px solid #333;
            margin: 10px 0;
        }


        .signature-name {
            margin-top: 10px;
            font-size: 14px;
        }


        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }


        @media print {
            body {
                padding: 0;
                margin: 0;
            }


            .invoice-container {
                border: none;
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>


<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="header">
            <div class="restaurant-name">HARI RESTAURANT</div>
            <div class="restaurant-info">169 Le Quang Chi, Hoa Xuan, Cam Le, Da Nang</div>
            <div class="restaurant-info">Điện thoại: 84+ 966 994 591</div>
            <div class="restaurant-info">Email: hainh.23ns@vku.udn.vn</div>
        </div>


        <!-- Invoice Title -->
        <div class="invoice-title">
            <h1>HÓA ĐƠN THANH TOÁN</h1>
            <div class="invoice-number">Số hóa đơn: #{{ $invoice->PaymentID }}</div>
        </div>


        <!-- Customer Information -->
        <div class="customer-info">
            <div class="info-row">
                <span class="label">Khách hàng:</span>
                <span>{{ $invoice->reservation->FullName }}</span>
            </div>
            <div class="info-row">
                <span class="label">Số điện thoại:</span>
                <span>{{ $invoice->reservation->Phone }}</span>
            </div>
            <div class="info-row">
                <span class="label">Ngày:</span>
                <span>{{ date('d/m/Y H:i', strtotime($invoice->CreatedAt)) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Bàn số:</span>
                <span>{{ $invoice->reservation->table ? $invoice->reservation->table->TableNumber : '--' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Phương thức TT:</span>
                <span>{{ $invoice->PaymentMethod }}</span>
            </div>
        </div>


        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">STT</th>
                    <th style="width: 40%">Tên món</th>
                    <th style="width: 15%">Số lượng</th>
                    <th style="width: 15%">Đơn giá</th>
                    <th style="width: 20%">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->reservation->reservationItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->menuItem->ItemName }}</td>
                        <td>{{ $item->Quantity }}</td>
                        <td class="text-right">{{ number_format($item->Price) }}đ</td>
                        <td class="text-right">{{ number_format($item->Price * $item->Quantity) }}đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row grand-total">
                <span>Tổng cộng:</span>
                <span>{{ number_format($invoice->Amount) }}đ</span>
            </div>
        </div>


        <!-- Payment Status -->
        <div class="payment-status">
            <div class="status-box {{ $invoice->Status === 'Đã thanh toán' ? 'paid' : 'pending' }}">
                Trạng thái: {{ $invoice->Status }}
            </div>
        </div>


        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Người lập hóa đơn</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ auth()->user()->name }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Khách hàng</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $invoice->reservation->FullName }}</div>
            </div>
        </div>


        <!-- Footer -->
        <div class="footer">
            Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!
        </div>
    </div>
</body>








</html>

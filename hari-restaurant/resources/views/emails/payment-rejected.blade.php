<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Thông Báo Từ Chối Thanh Toán</h2>
        </div>

        <div class="content">
            <p>Kính gửi {{ $reservation->FullName }},</p>

            <p>Chúng tôi rất tiếc phải thông báo rằng biên lai thanh toán của quý khách cho đơn đặt bàn
                #{{ $payment->PaymentCode }} đã bị từ chối.</p>

            <p><strong>Chi tiết đơn hàng:</strong></p>
            <ul>
                <li>Mã thanh toán: #{{ $payment->PaymentCode }}</li>
                <li>Số tiền: {{ number_format($payment->Amount) }}đ</li>
                <li>Thời gian đặt: {{ date('d/m/Y H:i', strtotime($reservation->ReservationDate)) }}</li>
            </ul>


            <p>Lý do có thể do:</p>
            <ul>
                <li>Biên lai thanh toán không hợp lệ hoặc không rõ ràng</li>
                <li>Số tiền thanh toán không khớp với hóa đơn</li>
                <li>Thông tin chuyển khoản không chính xác</li>
            </ul>


            <p>Quý khách vui lòng kiểm tra lại và thực hiện thanh toán lại hoặc liên hệ với chúng tôi để được hỗ trợ.
            </p>
        </div>

        <div class="footer">
            <p>Trân trọng,</p>
            <p>Hari Restaurant</p>
        </div> 
    </div>
</body>

</html>

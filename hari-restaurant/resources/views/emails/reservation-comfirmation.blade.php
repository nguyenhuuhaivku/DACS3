<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
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

        .details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Xác nhận đặt bàn</h2>
        </div>


        <p>Kính gửi khách hàng <strong>{{ $reservation->FullName }}</strong>,</p>


        <p>Cảm ơn bạn đã tin tưởng và đặt bàn tại nhà hàng chúng tôi. Dưới đây là thông tin đặt bàn của bạn:</p>


        <div class="details">
            <p><strong>Mã đặt bàn:</strong> {{ $reservation->ReservationCode }}</p>
            <p><strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($reservation->ReservationDate)->format('H:i - d/m/Y') }}</p>
            <p><strong>Số người:</strong> {{ $reservation->GuestCount }} người</p>
            <p><strong>Số điện thoại:</strong> {{ $reservation->Phone }}</p>
            @if($reservation->Note)
            <p><strong>Ghi chú:</strong> {{ $reservation->Note }}</p>
            @endif
        </div>


        @if($reservation->reservationItems->count() > 0)
        <div class="menu-items">
            <h3>Món ăn đã đặt:</h3>
            <ul>
                @foreach($reservation->reservationItems as $item)
                <li>{{ $item->menuItem->ItemName }} x {{ $item->Quantity }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <p>Trạng thái đơn hàng: <strong>{{ $reservation->Status }}</strong></p>

        <p>Vui lòng đến đúng giờ để chúng tôi phục vụ bạn tốt nhất. Nếu bạn cần thay đổi hoặc hủy đặt bàn, vui lòng liên hệ với chúng tôi trước ít nhất 2 giờ.</p>

        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>Mọi thắc mắc xin vui lòng liên hệ: <br>
                Số điện thoại: 0966994591<br>
                Email: nguyenhuuhai01122005@gmail.com</p>
        </div>
    </div>
</body>

</html>
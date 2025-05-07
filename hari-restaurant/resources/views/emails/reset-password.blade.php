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
            background-color: #764ba2;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .reset-code {
            background-color: #eee;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            letter-spacing: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Đặt lại mật khẩu</h1>
        </div>
        <div class="content">
            <h2>Xin chào,</h2>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            <p>Mã xác nhận của bạn là:</p>
            <div class="reset-code">
                {{ $resetCode }}
            </div>
            <p>Mã này sẽ hết hạn sau 15 phút.</p>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>© 2024 Hari Restaurant. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
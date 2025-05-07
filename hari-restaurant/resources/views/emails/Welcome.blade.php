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
            <h1>Chào mừng đến với Hari Restaurant!</h1>
        </div>
        <div class="content">
            <h2>Xin chào {{ $user->name }},</h2>
            <p>Chúng tôi rất vui mừng khi bạn đã đăng ký tài khoản tại Hari Restaurant.</p>
            <p>Thông tin tài khoản của bạn:</p>
            <ul>
                <li>Email: {{ $user->email }}</li>
                <li>Tên người dùng: {{ $user->name }}</li>
            </ul>
            <p>Hãy khám phá menu đa dạng và những ưu đãi hấp dẫn của chúng tôi!</p>
            <p><a href="{{ route('home') }}" style="background-color: #764ba2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Khám phá ngay</a></p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>© 2024 Hari Restaurant. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
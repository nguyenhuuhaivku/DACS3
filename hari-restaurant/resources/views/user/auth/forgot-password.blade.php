<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Hari Restaurant</title>
    <link rel="icon" href="{{ secure_asset('images/logo.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/notification.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form action="{{ route('password.email') }}" method="POST" class="forgot-password-form">
                <div class="form-header">
                    <i class="fas fa-lock"></i>
                    <h1>Quên mật khẩu</h1>
                </div>
                <div class="form-description">
                    <p>Nhập email của bạn để nhận mã xác thực</p>
                </div>
                @csrf
                <div class="form-group">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Nhập email của bạn"
                            value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </span>
                    @enderror
                </div>
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>
                    Gửi mã xác thực
                </button>
                <a href="{{ route('login') }}" class="back-to-login">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại đăng nhập
                </a>
            </form>
        </div>
    </div>
    @if(session('success') || session('error'))
    <div class="notification {{ session('success') ? 'success' : 'error' }}">
        <i class="fas fa-{{ session('success') ? 'check-circle' : 'exclamation-circle' }}"></i>
        {{ session('success') ?? session('error') }}
    </div>
    @endif
    <script>
        // Auto hide notifications
        const notification = document.querySelector('.notification');
        if (notification) {
            setTimeout(() => {
                notification.style.animation = 'fadeOut 0.5s ease-out forwards';
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }
    </script>
</body>

</html>
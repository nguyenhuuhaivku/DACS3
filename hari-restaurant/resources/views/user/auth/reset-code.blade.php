<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực mã - Hari Restaurant</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/notification.css') }}">
    <link rel="icon" href="{{ secure_asset('images/logo.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form action="{{ route('password.reset.verify') }}" method="POST" class="reset-code-form">
                @csrf
                <div class="form-header">
                    <i class="fas fa-key"></i>
                    <h1>Xác thực mã</h1>
                </div>
                <div class="form-description">
                    <p>Nhập mã xác thực đã được gửi đến email của bạn</p>
                </div>
                <input type="hidden" name="email" value="{{ session('email') }}">
                <div class="form-group">
                    <div class="input-group code-input">
                        <i class="fas fa-shield-alt"></i>
                        <input type="text" name="code" maxlength="6" required
                            pattern="\d{6}" title="Vui lòng nhập 6 chữ số"
                            placeholder="Nhập mã 6 số">
                    </div>
                    @error('code')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required minlength="8"
                            placeholder="Mật khẩu mới">
                    </div>
                    @error('password')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" required minlength="8"
                            placeholder="Xác nhận mật khẩu mới">
                    </div>
                </div>


                <button type="submit">
                    <i class="fas fa-check-circle"></i>
                    Xác nhận
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


        // Format code input
        const codeInput = document.querySelector('input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            });
        }
    </script>
</body>

</html>
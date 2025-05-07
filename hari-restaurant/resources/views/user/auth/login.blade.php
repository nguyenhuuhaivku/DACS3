<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hari Restaurant')</title>
    <link rel="icon" href="{{ secure_asset('images/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ secure_asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/notification.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="auth-container">
        <!-- Hiển thị tất cả lỗi ở đầu form -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif


        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
        @endif


        <div class="auth-tabs">
            <button class="tab-btn active" data-tab="login">
                <i class="fas fa-sign-in-alt"></i>
                Đăng nhập
            </button>
            <button class="tab-btn" data-tab="register">
                <i class="fas fa-user-plus"></i>
                Đăng ký
            </button>
        </div>


        <div class="auth-content">
            <!-- Form đăng nhập -->
            <div class="tab-content active" id="login">
                <form action="/dang-nhap" method="POST">
                    <h1>Đăng Nhập</h1>
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Mật khẩu" name="password" required />
                        </div>
                    </div>

                    <a href="{{ route('password.request') }}" class="forgot-password">
                        <i class="fas fa-key"></i>
                        Quên mật khẩu?
                    </a>
                    <button type="submit">
                        <i class="fas fa-sign-in-alt"></i>
                        Đăng Nhập
                    </button>
                </form>
            </div>


            <!-- Form đăng ký -->
            <div class="tab-content" id="register">
                <form action="/dang-ki" method="POST">
                    <h1>Tạo tài khoản</h1>
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Họ tên" name="name" value="{{ old('name') }}" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Mật khẩu" name="password" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Xác nhận mật khẩu"
                                name="password_confirmation" required />
                        </div>
                    </div>

                    <button type="submit">
                        <i class="fas fa-user-plus"></i>
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script>
        // Xử lý chuyển tab
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');


        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.add('active');
            });
        });


        // Xử lý ẩn alert sau 3 giây
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'slideOut 0.5s ease-out forwards';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 3000);
        });
    </script>
</body>

</html>
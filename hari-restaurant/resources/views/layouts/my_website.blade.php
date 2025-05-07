<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ secure_asset('images/logo.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <title>@yield('title', 'Hari Restaurant')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ secure_asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/animation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animation.css') }}">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .background {
        background-image: url("../images/artem-beliaikin-TSS-1aqoRXE-unsplash.jpg");
        background-size: cover;
        background-position: center;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<body class="text-gray-200 font-sans">
    <div class="loader-wrapper">
        <div class="clock-loader">
            <div class="plate-clock">
                @for ($i = 1; $i <= 12; $i++)
                    <div class="hour-marks" style="transform: rotate({{ $i * 30 }}deg)">
            </div>
            @endfor
        </div>
        <!-- Kim đồng hồ -->
        <div class="utensil fork"></div>
        <div class="utensil knife"></div>
        <div class="utensil spoon"></div>
    </div>
    </div>
    @include('partials.header')


    <main class="background flex flex-col justify-center items-center text-center px-8">
        @yield('content')
    </main>
    @include('partials.featured_dishes')
    @include('partials.about')
    @include('partials.seasonal_menu')
    @include('partials.special_service')
    @include('partials.footer')
    <script src="{{ secure_asset('js/animation.js') }}"></script>
    <script src="{{ asset('js/animation.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
            easing: 'ease-out'
        });
    </script>
    <script src="{{ secure_asset('js/counter.js') }}"></script>
    <script src="{{ asset('js/counter.js') }}"></script>

</body>

</html>
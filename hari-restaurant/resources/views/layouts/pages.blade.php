<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ secure_asset('images/logo.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <title>@yield('title', 'Hari Restaurant')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ secure_asset('css/animation.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class=" font-sans">
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

    <main class="section flex flex-col justify-center items-center text-center px-8 ">
        @yield('content')
    </main>

    @include('partials.footer')
    @yield('scripts')
    <script src="{{ secure_asset('js/animation.js') }}"></script>
    <script src="{{ asset('js/animation.js') }}"></script>
</body>

</html>

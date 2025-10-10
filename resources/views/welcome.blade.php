<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SokishiX - Chat Realtime</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>
<body>
    <main>
        <div class="welcome-card">
            <div class="logo-circle shadow">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div class="brand">
                Sokishi<span class="highlight">X</span>
            </div>
            <div class="desc">
                Nền tảng trò chuyện hiện đại, realtime, đơn giản và an toàn.<br>
                Kết nối mọi người trên mọi thiết bị.
            </div>
            <div class="links">
                    @if(Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="btn-main">Vào chat</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-main">Đăng nhập</a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-alt">Đăng ký</a>
                            @endif
                        @endauth
                    @endif
            </div>
        </div>
    </main>
</body>
</html>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/sokishix-app.css', 'resources/js/app.js'])
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm py-0 border-0">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <!-- Left: Logo only -->
            <div class="d-flex align-items-center flex-grow-1" style="min-width:0;">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/home') }}" style="gap: 0.5rem;">
                    <div class="brand-logo d-flex align-items-center justify-content-center">
                        <div class="logo-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" 
                             style="width: 38px; height: 38px; border-radius: 50%; font-size: 18px; min-width:38px;">
                             S
                        </div>
                    </div>
                </a>
            </div>
            <!-- Right: Name and User Info -->
            <div class="d-flex align-items-center gap-2">
                <!-- App Name, visible at md and up -->
                <span class="fw-bold h4 mb-0 d-none d-md-inline px-3 text-end" style="white-space:nowrap;">
                    <span class="text-dark">Sokishi</span><span class="text-warning">X</span>
                </span>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav flex-row align-items-center gap-1 mb-0" style="list-style:none;">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="btn btn-link px-3 py-2 nav-link text-primary" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>{{ __('Login') }}
                                </a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-primary px-3 py-2 nav-link text-white" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>{{ __('Register') }}
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item d-none d-md-block">
                            <span class="badge bg-gradient-primary px-3 py-2 align-items-center" style="font-weight: 500; font-size: 14px;">
                                <i class="fas fa-circle text-success me-1" style="font-size: 10px; vertical-align: middle;"></i>
                                Online
                            </span>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" 
                               class="nav-link dropdown-toggle d-flex align-items-center px-2 py-1" 
                               href="#" role="button" aria-haspopup="true" aria-expanded="false" v-pre
                               style="gap: 0.5rem;"
                            >
                                <span class="user-avatar d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                                    @if(Auth::user()->profile_image && Auth::user()->profile_image !== 'default_image.png')
                                        <img src="{{ Auth::user()->getAvatarUrl() }}" 
                                             alt="{{ Auth::user()->name }}" 
                                             class="rounded-circle user-avatar-img" width="38" height="38" style="object-fit: cover;">
                                    @else
                                        <div class="user-avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 18px;">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </span>
                                <div class="d-none d-lg-flex flex-column align-items-start me-1">
                                    <span class="fw-medium user-name" style="line-height:1">{{ Auth::user()->name }}</span>
                                    <small class="user-status text-muted" style="line-height:1;font-size:12px;">{{ Auth::user()->email }}</small>
                                </div>
                                <i class="fas fa-chevron-down text-muted ms-1"></i>
                            </a>
                            <!-- Custom dropdown menu: hidden by default, show on click -->
                            <div class="dropdown-menu dropdown-menu-end user-dropdown mt-1" aria-labelledby="navbarDropdown" style="min-width: 220px; border-radius: 14px; display: none;">
                                <!-- User Info Header -->
                                <div class="dropdown-header user-dropdown-header pb-2 mb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            @if(Auth::user()->profile_image && Auth::user()->profile_image !== 'default_image.png')
                                                <img src="{{ Auth::user()->getAvatarUrl() }}" 
                                                     alt="{{ Auth::user()->name }}" 
                                                     class="rounded-circle user-avatar-img" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="user-avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 20px;">
                                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 user-name">{{ Auth::user()->name }}</h6>
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation Links -->
                                <a class="dropdown-item" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user-circle me-2"></i>
                                    Thông tin cá nhân
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-edit me-2"></i>
                                    Chỉnh sửa thông tin
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                                    <i class="fas fa-key me-2"></i>
                                    Đổi mật khẩu
                                </a>

                                <div class="dropdown-divider my-2"></div>

                                <a class="dropdown-item" href="#" onclick="alert('Tính năng đang phát triển')">
                                    <i class="fas fa-cog me-2"></i>
                                    Cài đặt
                                </a>
                                <a class="dropdown-item" href="#" onclick="alert('Tính năng đang phát triển')">
                                    <i class="fas fa-bell me-2"></i>
                                    Thông báo
                                </a>

                                <div class="dropdown-divider my-2"></div>

                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Đăng xuất
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User dropdown show/hide (Profile dropdown)
    const userDropdown = document.getElementById('navbarDropdown');
    const dropdownMenu = document.querySelector('.user-dropdown');
    let dropdownVisible = false;

    if (userDropdown && dropdownMenu) {
        // Hide menu by default (just in case)
        dropdownMenu.style.display = 'none';
        dropdownMenu.style.opacity = '0';
        dropdownMenu.style.transform = 'translateY(-10px)';
        dropdownMenu.style.transition = 'all 0.25s cubic-bezier(.4,0,.2,1)';

        // Toggle dropdown on click
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            dropdownVisible = !dropdownVisible;

            if (dropdownVisible) {
                dropdownMenu.style.display = 'block';
                setTimeout(() => {
                    dropdownMenu.style.opacity = '1';
                    dropdownMenu.style.transform = 'translateY(0)';
                }, 10);
                userDropdown.setAttribute('aria-expanded', 'true');
            } else {
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    dropdownMenu.style.display = 'none';
                }, 220);
                userDropdown.setAttribute('aria-expanded', 'false');
            }
        });

        // Auto close dropdown when clicking outside
        document.addEventListener('mousedown', function(e) {
            if (dropdownVisible) {
                // If click is not inside dropdownMenu or userDropdown
                if (!dropdownMenu.contains(e.target) && !userDropdown.contains(e.target)) {
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.transform = 'translateY(-10px)';
                    userDropdown.setAttribute('aria-expanded', 'false');
                    setTimeout(() => {
                        dropdownMenu.style.display = 'none';
                    }, 200);
                    dropdownVisible = false;
                }
            }
        });

        // Auto close on ESC
        document.addEventListener('keydown', function(e) {
            if (dropdownVisible && e.key === "Escape") {
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.transform = 'translateY(-10px)';
                userDropdown.setAttribute('aria-expanded', 'false');
                setTimeout(() => {
                    dropdownMenu.style.display = 'none';
                }, 200);
                dropdownVisible = false;
            }
        });
    }

    // Add loading state for dropdown items
    const dropdownItems = document.querySelectorAll('.user-dropdown .dropdown-item[href]');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!this.href.includes('#')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>Đang tải...</span>';
            }
        });
    });
});
</script>

</body>
</html>

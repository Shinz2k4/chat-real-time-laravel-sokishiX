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
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <div class="brand-logo me-2">
                    <div class="logo-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; border-radius: 50%; font-weight: 900; font-size: 16px;">S</div>
                </div>
                <span class="fw-bold text-gradient">Sokishi<span class="text-warning">X</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <!-- User Avatar -->
                                <div class="user-avatar me-2">
                                    @if(Auth::user()->profile_image && Auth::user()->profile_image !== 'default_image.png')
                                        <img src="{{ Auth::user()->getAvatarUrl() }}" 
                                             alt="{{ Auth::user()->name }}" 
                                             class="rounded-circle user-avatar-img">
                                    @else
                                        <div class="user-avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <!-- User Name -->
                                <div class="user-info">
                                    <span class="user-name">{{ Auth::user()->name }}</span>
                                    <small class="user-status text-muted d-block">Online</small>
                                </div>
                                <!-- Dropdown Arrow -->
                                <i class="fas fa-chevron-down ms-2 text-muted"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="navbarDropdown">
                                <!-- User Info Header -->
                                <div class="dropdown-header user-dropdown-header">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            @if(Auth::user()->profile_image && Auth::user()->profile_image !== 'default_image.png')
                                                <img src="{{ Auth::user()->getAvatarUrl() }}" 
                                                     alt="{{ Auth::user()->name }}" 
                                                     class="rounded-circle user-avatar-img">
                                            @else
                                                <div class="user-avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
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
                                
                                <div class="dropdown-divider"></div>
                                
                                <!-- Profile Actions -->
                                <a class="dropdown-item" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user-circle me-3"></i>
                                    <span>Thông tin cá nhân</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-edit me-3"></i>
                                    <span>Chỉnh sửa thông tin</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                                    <i class="fas fa-key me-3"></i>
                                    <span>Đổi mật khẩu</span>
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                
                                <!-- Settings -->
                                <a class="dropdown-item" href="#" onclick="alert('Tính năng đang phát triển')">
                                    <i class="fas fa-cog me-3"></i>
                                    <span>Cài đặt</span>
                                </a>
                                <a class="dropdown-item" href="#" onclick="alert('Tính năng đang phát triển')">
                                    <i class="fas fa-bell me-3"></i>
                                    <span>Thông báo</span>
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                
                                <!-- Logout -->
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-3"></i>
                                    <span>Đăng xuất</span>
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
    // User dropdown enhancement
    const userDropdown = document.getElementById('navbarDropdown');
    const dropdownMenu = document.querySelector('.user-dropdown');
    
    if (userDropdown && dropdownMenu) {
        // Add smooth animations
        userDropdown.addEventListener('show.bs.dropdown', function() {
            dropdownMenu.style.opacity = '0';
            dropdownMenu.style.transform = 'translateY(-10px)';
        });
        
        userDropdown.addEventListener('shown.bs.dropdown', function() {
            dropdownMenu.style.transition = 'all 0.3s ease';
            dropdownMenu.style.opacity = '1';
            dropdownMenu.style.transform = 'translateY(0)';
        });
        
        userDropdown.addEventListener('hide.bs.dropdown', function() {
            dropdownMenu.style.opacity = '0';
            dropdownMenu.style.transform = 'translateY(-10px)';
        });
        
        // Add click outside to close
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
                const bsDropdown = new bootstrap.Dropdown(userDropdown);
                bsDropdown.hide();
            }
        });
    }
    
    // Add loading state for dropdown items
    const dropdownItems = document.querySelectorAll('.user-dropdown .dropdown-item[href]');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!this.href.includes('#')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-3"></i><span>Đang tải...</span>';
            }
        });
    });
});
</script>

</body>
</html>

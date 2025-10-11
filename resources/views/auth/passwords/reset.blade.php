@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Đặt lại mật khẩu
                    </h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="fas fa-key text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Tạo mật khẩu mới</h5>
                        <p class="text-muted">
                            Nhập mật khẩu mới cho tài khoản <strong>{{ $email }}</strong>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Mật khẩu mới
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Nhập mật khẩu mới"
                                   required
                                   autocomplete="new-password"
                                   autofocus>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Xác nhận mật khẩu
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Nhập lại mật khẩu mới"
                                   required
                                   autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Yêu cầu mật khẩu:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ít nhất 8 ký tự</li>
                                <li>Nên bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Đặt lại mật khẩu
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Nhớ mật khẩu? 
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Đăng nhập ngay
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    // Real-time password confirmation validation
    passwordConfirmation.addEventListener('input', function() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    });
    
    password.addEventListener('input', function() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    });
});
</script>
@endsection
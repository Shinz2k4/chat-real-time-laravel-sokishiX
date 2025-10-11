@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Quên mật khẩu
                    </h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
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
                        <i class="fas fa-lock text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Đặt lại mật khẩu</h5>
                        <p class="text-muted">
                            Nhập địa chỉ email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>
                                Địa chỉ email
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Nhập địa chỉ email của bạn"
                                   required
                                   autocomplete="email"
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Gửi link đặt lại mật khẩu
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
@endsection
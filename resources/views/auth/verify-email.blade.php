@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        Xác thực Email
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
                        <i class="fas fa-shield-alt text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Nhập mã xác thực</h5>
                        <p class="text-muted">
                            Chúng tôi đã gửi mã xác thực 6 chữ số đến email <strong>{{ $email }}</strong>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('verify.email') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-4">
                            <label for="code" class="form-label">
                                <i class="fas fa-key me-1"></i>
                                Mã xác thực
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}" 
                                   placeholder="Nhập 6 chữ số"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   required
                                   autocomplete="off"
                                   style="font-size: 1.5rem; letter-spacing: 0.5rem; font-family: 'Courier New', monospace;">
                            @error('code')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Xác thực Email
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted small">
                            <i class="fas fa-clock me-1"></i>
                            Mã xác thực có hiệu lực trong 10 phút
                        </p>
                        <form method="POST" action="{{ route('resend.verification') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <button type="submit" class="btn btn-link text-decoration-none">
                                <i class="fas fa-redo me-1"></i>
                                Gửi lại mã xác thực
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    
    // Chỉ cho phép nhập số
    codeInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Tự động focus vào input
    codeInput.focus();
    
    // Tự động submit khi nhập đủ 6 số
    codeInput.addEventListener('input', function(e) {
        if (this.value.length === 6) {
            this.form.submit();
        }
    });
});
</script>
@endsection

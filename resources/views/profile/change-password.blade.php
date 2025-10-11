@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-gradient-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Đổi mật khẩu
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

                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Bảo mật tài khoản</h5>
                        <p class="text-muted">
                            Thay đổi mật khẩu để đảm bảo tài khoản của bạn được bảo mật
                        </p>
                    </div>

                    <form method="POST" action="{{ route('profile.update-password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Mật khẩu hiện tại <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Nhập mật khẩu hiện tại"
                                       required
                                       autocomplete="current-password"
                                       autofocus>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-key me-1"></i>
                                Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Nhập mật khẩu mới"
                                       required
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password_icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            
                            <!-- Password Strength Indicator -->
                            <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="strengthText">Nhập mật khẩu để kiểm tra độ mạnh</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-check-circle me-1"></i>
                                Xác nhận mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Nhập lại mật khẩu mới"
                                       required
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            
                            <!-- Password Match Indicator -->
                            <div class="password-match mt-2" id="passwordMatch" style="display: none;">
                                <small id="matchText"></small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Yêu cầu mật khẩu mạnh:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ít nhất 8 ký tự</li>
                                <li>Nên bao gồm chữ hoa và chữ thường</li>
                                <li>Nên bao gồm số và ký tự đặc biệt</li>
                                <li>Không sử dụng thông tin cá nhân</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Quay lại
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn" disabled>
                                <i class="fas fa-save me-2"></i>
                                Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(password) {
    let strength = 0;
    let strengthText = '';
    let strengthColor = '';
    
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]/)) strength += 1;
    if (password.match(/[A-Z]/)) strength += 1;
    if (password.match(/[0-9]/)) strength += 1;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
    
    switch (strength) {
        case 0:
        case 1:
            strengthText = 'Rất yếu';
            strengthColor = 'bg-danger';
            break;
        case 2:
            strengthText = 'Yếu';
            strengthColor = 'bg-warning';
            break;
        case 3:
            strengthText = 'Trung bình';
            strengthColor = 'bg-info';
            break;
        case 4:
            strengthText = 'Mạnh';
            strengthColor = 'bg-success';
            break;
        case 5:
            strengthText = 'Rất mạnh';
            strengthColor = 'bg-success';
            break;
    }
    
    return {
        strength: (strength / 5) * 100,
        text: strengthText,
        color: strengthColor
    };
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('passwordMatch');
    const matchText = document.getElementById('matchText');
    const submitBtn = document.getElementById('submitBtn');
    
    if (confirmation.length > 0) {
        matchDiv.style.display = 'block';
        
        if (password === confirmation) {
            matchText.innerHTML = '<i class="fas fa-check text-success me-1"></i>Mật khẩu khớp';
            matchText.className = 'text-success';
            submitBtn.disabled = false;
        } else {
            matchText.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Mật khẩu không khớp';
            matchText.className = 'text-danger';
            submitBtn.disabled = true;
        }
    } else {
        matchDiv.style.display = 'none';
        submitBtn.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmationField = document.getElementById('password_confirmation');
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    passwordField.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length > 0) {
            strengthDiv.style.display = 'block';
            const strength = checkPasswordStrength(password);
            
            strengthBar.style.width = strength.strength + '%';
            strengthBar.className = 'progress-bar ' + strength.color;
            strengthText.textContent = 'Độ mạnh: ' + strength.text;
        } else {
            strengthDiv.style.display = 'none';
        }
        
        checkPasswordMatch();
    });
    
    confirmationField.addEventListener('input', checkPasswordMatch);
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value;
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        
        if (!currentPassword || !password || !confirmation) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin!');
            return false;
        }
        
        if (password !== confirmation) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp!');
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('Mật khẩu phải có ít nhất 8 ký tự!');
            return false;
        }
    });
});
</script>

<style>
.input-group .btn {
    border-left: 0;
}

.input-group .form-control:focus + .btn {
    border-color: #6366f1;
}

.password-strength .progress {
    background-color: #e9ecef;
}

.password-match {
    font-weight: 500;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endsection

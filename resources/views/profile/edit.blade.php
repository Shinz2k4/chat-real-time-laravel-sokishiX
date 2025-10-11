@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Chỉnh sửa thông tin cá nhân
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

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Current Avatar -->
                            <div class="col-md-4 text-center">
                                <div class="current-avatar mb-4">
                                    <h6 class="text-muted mb-3">Ảnh đại diện hiện tại</h6>
                                    @if($user->profile_image && $user->profile_image !== 'default_image.png')
                                        <img src="{{ $user->getAvatarUrl() }}" 
                                             alt="{{ $user->name }}" 
                                             class="profile-image-preview rounded-circle shadow"
                                             id="currentAvatar">
                                    @else
                                        <div class="profile-placeholder-preview bg-primary text-white rounded-circle shadow d-flex align-items-center justify-content-center"
                                             id="currentAvatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="upload-section">
                                    <label for="profile_image" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-camera me-2"></i>
                                        Chọn ảnh mới
                                    </label>
                                    <input type="file" 
                                           class="form-control d-none" 
                                           id="profile_image" 
                                           name="profile_image" 
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    <small class="text-muted d-block mt-2">
                                        JPG, PNG, GIF tối đa 2MB
                                    </small>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           placeholder="Nhập họ và tên"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        Email
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-lg" 
                                           id="email" 
                                           value="{{ $user->email }}" 
                                           disabled>
                                    <small class="text-muted">Email không thể thay đổi. Liên hệ admin nếu cần thiết.</small>
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>
                                        Số điện thoại
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}" 
                                           placeholder="Nhập số điện thoại">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Lưu ý:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Ảnh đại diện sẽ được tối ưu hóa tự động</li>
                                        <li>Kích thước khuyến nghị: 400x400px</li>
                                        <li>Định dạng hỗ trợ: JPG, PNG, GIF</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const currentAvatar = document.getElementById('currentAvatar');
            
            if (currentAvatar.tagName === 'IMG') {
                currentAvatar.src = e.target.result;
            } else {
                // Replace placeholder with image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'profile-image-preview rounded-circle shadow';
                img.alt = 'Preview';
                currentAvatar.parentNode.replaceChild(img, currentAvatar);
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    
    form.addEventListener('submit', function(e) {
        if (nameInput.value.trim().length === 0) {
            e.preventDefault();
            nameInput.classList.add('is-invalid');
            nameInput.focus();
            return false;
        }
    });
    
    nameInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            this.classList.remove('is-invalid');
        }
    });
});
</script>

<style>
.profile-image-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 3px solid #fff;
}

.profile-placeholder-preview {
    width: 120px;
    height: 120px;
    font-size: 3rem;
    margin: 0 auto;
}

.upload-section {
    padding: 20px;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    background: #f8f9fa;
}

.upload-section:hover {
    border-color: #6366f1;
    background: #f0f0ff;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}
</style>
@endsection

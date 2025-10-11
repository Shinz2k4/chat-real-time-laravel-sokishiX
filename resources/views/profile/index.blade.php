@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        Thông tin cá nhân
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

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Profile Image Section -->
                        <div class="col-md-4 text-center">
                            <div class="profile-image-container mb-4">
                                @if($user->profile_image && $user->profile_image !== 'default_image.png')
                                    <img src="{{ $user->getAvatarUrl() }}" 
                                         alt="{{ $user->name }}" 
                                         class="profile-image-large rounded-circle shadow">
                                @else
                                    <div class="profile-placeholder-large bg-primary text-white rounded-circle shadow d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="btn-group-vertical w-100" role="group">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary mb-2">
                                    <i class="fas fa-edit me-2"></i>
                                    Chỉnh sửa thông tin
                                </a>
                                <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning mb-2">
                                    <i class="fas fa-key me-2"></i>
                                    Đổi mật khẩu
                                </a>
                                @if($user->hasCustomAvatar())
                                    <form action="{{ route('profile.delete-avatar') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Bạn có chắc muốn xóa ảnh đại diện?')">
                                            <i class="fas fa-trash me-2"></i>
                                            Xóa ảnh đại diện
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="col-md-8">
                            <div class="profile-info">
                                <h5 class="text-primary mb-4">
                                    <i class="fas fa-user me-2"></i>
                                    Thông tin cá nhân
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong class="text-muted">Họ và tên:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        <span class="fs-5">{{ $user->name }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong class="text-muted">Email:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        <span class="fs-5">{{ $user->email }}</span>
                                        <small class="text-muted d-block">Địa chỉ email đăng nhập</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong class="text-muted">Số điện thoại:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        <span class="fs-5">{{ $user->phone ?: 'Chưa cập nhật' }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong class="text-muted">Ngày tham gia:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        <span class="fs-5">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong class="text-muted">Cập nhật lần cuối:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        <span class="fs-5">{{ \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-cogs me-2"></i>
                                Thao tác nhanh
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-edit text-primary mb-3" style="font-size: 2rem;"></i>
                                            <h6 class="card-title">Chỉnh sửa thông tin</h6>
                                            <p class="card-text text-muted small">Cập nhật tên, số điện thoại và ảnh đại diện</p>
                                            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Chỉnh sửa</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-key text-warning mb-3" style="font-size: 2rem;"></i>
                                            <h6 class="card-title">Đổi mật khẩu</h6>
                                            <p class="card-text text-muted small">Thay đổi mật khẩu để bảo mật tài khoản</p>
                                            <a href="{{ route('profile.change-password') }}" class="btn btn-warning btn-sm">Đổi mật khẩu</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-shield-alt text-success mb-3" style="font-size: 2rem;"></i>
                                            <h6 class="card-title">Bảo mật</h6>
                                            <p class="card-text text-muted small">Quản lý bảo mật và quyền riêng tư</p>
                                            <button class="btn btn-success btn-sm" disabled>Sắp có</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-image-large {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 4px solid #fff;
}

.profile-placeholder-large {
    width: 150px;
    height: 150px;
    font-size: 4rem;
    margin: 0 auto;
}

.profile-image-container {
    position: relative;
    display: inline-block;
}

.profile-info .row {
    border-bottom: 1px solid #f0f0f0;
    padding: 10px 0;
}

.profile-info .row:last-child {
    border-bottom: none;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection

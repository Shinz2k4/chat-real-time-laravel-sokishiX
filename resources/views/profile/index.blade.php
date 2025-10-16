@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card profile-card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        Hồ sơ của bạn
                    </h4>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm"><i class="fas fa-edit me-1"></i>Chỉnh sửa</a>
                        <a href="{{ route('profile.change-password') }}" class="btn btn-outline-light btn-sm"><i class="fas fa-key me-1"></i>Mật khẩu</a>
                    </div>
                </div>
                <div class="card-body p-4">
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

                    <div class="row g-4">
                        <!-- Profile Image Section -->
                        <div class="col-md-4">
                            <div class="d-flex flex-column align-items-center">
                                <div class="profile-image-container mb-3">
                                @if($user->profile_image && $user->profile_image !== 'default_image.png')
                                    <img src="{{ $user->getAvatarUrl(['width'=>300,'height'=>300]) }}" 
                                         alt="{{ $user->name }}" 
                                         class="profile-image-large rounded-circle shadow"
                                         style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                @else
                                    <div class="profile-placeholder-large bg-primary text-white rounded-circle shadow d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                </div>
                                <div class="text-center">
                                    <div class="badge bg-light text-dark border me-2"><i class="fas fa-at me-1"></i>{{ $user->username }}</div>
                                    <div class="badge bg-light text-dark border"><i class="fas fa-calendar-alt me-1"></i>Tham gia {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-2"></i>
                                    Chỉnh sửa thông tin
                                </a>
                                <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-key me-2"></i>
                                    Đổi mật khẩu
                                </a>
                                @if($user->hasCustomAvatar())
                                    <form action="{{ route('profile.delete-avatar') }}" method="POST" class="d-grid">
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
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="text-primary mb-0 d-flex align-items-center gap-2">
                                        <i class="fas fa-user"></i>
                                        Thông tin cá nhân
                                    </h5>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary"><i class="fas fa-pen me-1"></i>Sửa</a>
                                </div>

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

                                <div class="row mb-0">
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
                            <h5 class="text-primary mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-cogs"></i>
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
    width: 140px;
    height: 140px;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

.profile-placeholder-large {
    width: 140px;
    height: 140px;
    font-size: 3.5rem;
    margin: 0 auto;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

.profile-image-container {
    position: relative;
    display: inline-block;
}

.profile-info .row {
    border-bottom: 1px dashed #e5e7eb;
    padding: 12px 0;
}

.profile-info .row:last-child {
    border-bottom: none;
}

.card { transition: transform 0.2s ease-in-out, box-shadow .2s ease; border-radius: 14px; }
.card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
.profile-card .card-header { padding: 16px 20px; }
.profile-card .card-body { background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%); }
.badge.border { border: 1px solid #e5e7eb; border-radius: 999px; padding: 6px 10px; font-weight: 600; }
.btn-outline-warning { border-color: #f59e0b; color: #b45309; }
.btn-outline-warning:hover { background: #f59e0b; color: #fff; }
.btn-outline-primary { border-color: #6366f1; color: #4338ca; }
.btn-outline-primary:hover { background: #6366f1; color: #fff; }
.btn-outline-danger { border-color: #ef4444; color: #991b1b; }
.btn-outline-danger:hover { background: #ef4444; color: #fff; }
.btn-outline-light { border-color: rgba(255,255,255,0.6); }

/* Professional modal image viewer */
.avatar-viewer { background: #0f172a; min-height: 60vh; max-height: 75vh; overflow: hidden; }
.avatar-viewer-img { max-width: 100%; max-height: 75vh; object-fit: contain; }
</style>
<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      
    </div>
  </div>
</div>
@endsection

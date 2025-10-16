@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-user me-2"></i>Hồ sơ</h4>
                    <a href="{{ route('home') }}" class="btn btn-light btn-sm">Quay lại</a>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($user->profile_image && $user->profile_image !== 'default_image.png')
                            <img src="{{ $user->getAvatarUrl(['width'=>100,'height'=>100]) }}" class="rounded-circle" width="80" height="80" style="object-fit:cover;cursor:pointer" data-bs-toggle="modal" data-bs-target="#avatarModal">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:80px;height:80px;font-size:2rem;">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $user->name }} <small class="text-muted">@{{ $user->username }}</small></h5>
                            <div class="text-muted">{{ $user->email }}</div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Tham gia:</strong> {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</li>
                        <li class="list-group-item"><strong>Số điện thoại:</strong> {{ $user->phone ?: 'Chưa cập nhật' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card { border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
.card-header { border-top-left-radius: 14px; border-top-right-radius: 14px; }
.rounded-circle { box-shadow: 0 6px 18px rgba(0,0,0,0.12); }
.avatar-viewer { background: #0f172a; min-height: 60vh; max-height: 75vh; overflow: hidden; }
.avatar-viewer-img { max-width: 100%; max-height: 75vh; object-fit: contain; }
</style>

<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ảnh đại diện</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        @if($user->profile_image && $user->profile_image !== 'default_image.png')
          <div class="avatar-viewer d-flex align-items-center justify-content-center">
            <img src="{{ $user->getAvatarUrl(['width'=>1200,'height'=>1200]) }}" class="avatar-viewer-img" alt="avatar">
          </div>
        @else
          <div class="avatar-viewer d-flex align-items-center justify-content-center">
            <div class="text-muted">Chưa có ảnh</div>
          </div>
        @endif
      </div>
    </div>
  </div>
  </div>
@endsection



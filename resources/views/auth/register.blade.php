@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">{{ __('Register') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" placeholder="Enter your name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="username" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>

                                <div class="col-md-6">
                                    <input id="username" placeholder="Choose a unique username" type="text"
                                           class="form-control @error('username') is-invalid @enderror" name="username"
                                           value="{{ old('username') }}" required autocomplete="username">

                                    @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           placeholder="Enter your email address"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="phone"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>

                                <div class="col-md-6">
                                    <input id="phone" type="tel"
                                           placeholder="Enter your phone number"
                                           class="form-control @error('phone') is-invalid @enderror" name="phone"
                                           value="{{ old('phone') }}" required autocomplete="phone">

                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           placeholder="Enter your password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           placeholder="Confirm your password"
                                           name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="profile-image"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Profile Image') }}</label>

                                <div class="col-md-6">
                                    <input id="profile-image" type="file" class="form-control" name="profile_image">
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action="{{ route('register') }}"]');
    if (!form) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // chặn submit mặc định để tự gửi Fetch

        // kiểm tra HTML5 trước
        if (!form.checkValidity()) {
        form.reportValidity();
        return;
        }

        submitBtn.disabled = true;
        const original = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng ký...';

        try {
        const res = await fetch("{{ route('register') }}", {
            method: 'POST',
            headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
            },
            body: new FormData(form)
        });

        if (res.ok) {
            // Laravel sẽ trả 302 -> Fetch tự follow -> res.redirected = true
            // Nếu controller trả redirect đến /verify-email?email=..., chuyển trang theo URL cuối cùng
            if (res.redirected) {
            window.location.href = res.url;
            return;
            }
            // Trường hợp controller trả HTML: render ra
            const html = await res.text();
            document.open(); document.write(html); document.close();
            return;
        }

        // 422: lỗi validate -> hiển thị lỗi
        if (res.status === 422) {
            const data = await res.json().catch(() => ({}));
            const errs = (data && data.errors) ? data.errors : {};
            // clear trạng thái cũ
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            Object.entries(errs).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;
            input.classList.add('is-invalid');
            const fb = document.createElement('span');
            fb.className = 'invalid-feedback';
            fb.role = 'alert';
            fb.innerHTML = `<strong>${Array.isArray(messages) ? messages[0] : messages}</strong>`;
            input.insertAdjacentElement('afterend', fb);
            });
            return;
        }

        // lỗi khác
        alert('Đăng ký thất bại. Mã: ' + res.status);
        } catch (err) {
        console.error(err);
        alert('Không thể gửi yêu cầu. Kiểm tra kết nối hoặc tắt extension gây xung đột.');
        } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = original;
        }
    });
    });
    </script>
@endsection

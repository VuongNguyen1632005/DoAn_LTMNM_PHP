@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill text-uppercase mb-2">Tài khoản</span>
                    <h2 class="fw-bold">Đăng nhập</h2>
                    <p class="text-muted small">Chào mừng bạn quay lại với thời trang Smart Casual</p>
                </div>

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="login" class="form-label fw-bold small">Email hoặc Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login', 'demo1@gmail.com') }}" placeholder="demo1@gmail.com" required autofocus>
                        @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="password123" placeholder="••••••••" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark rounded-pill w-100 py-3 fw-bold mb-3 shadow">
                        Đăng nhập
                    </button>

                    <div class="alert alert-light border small text-muted mb-3">
                        <strong>Tài khoản Demo Khách hàng:</strong><br>
                        Email: <code>demo1@gmail.com</code> | Mật khẩu: <code>password123</code>
                    </div>

                    <div class="text-center small text-muted">
                        Chưa có tài khoản? <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

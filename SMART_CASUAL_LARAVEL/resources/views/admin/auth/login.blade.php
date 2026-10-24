<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Đăng nhập Quản trị' }} | Smart Casual Admin</title>
    <link rel="stylesheet" href="/assets/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendors/fontawesome/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #14221d 0%, #243b32 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
        }
        .admin-icon {
            width: 64px;
            height: 64px;
            background: #f4f6f8;
            color: #14221d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center">
        <div class="admin-icon">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h3 class="fw-bold mb-1">SMART CASUAL</h3>
        <p class="text-muted small mb-4">Cổng thông tin Quản trị hệ thống (Admin Portal)</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="username" class="form-label small fw-bold">Tên đăng nhập quản trị <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" id="username" name="username" value="{{ old('username', 'admin') }}" placeholder="admin" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label small fw-bold">Mật khẩu <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-muted"></i></span>
                <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" value="admin123" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-dark w-100 py-2 fw-bold rounded-pill mb-3 shadow">
            Đăng nhập hệ thống <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>

        <div class="alert alert-light border small text-muted mb-3">
            <strong>Tài khoản Demo Admin:</strong><br>
            Tên đăng nhập: <code>admin</code> (Manager) hoặc <code>staff</code> (Staff)<br>
            Mật khẩu: <code>admin123</code> / <code>staff123</code>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-muted small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i>Quay lại trang bán hàng
            </a>
        </div>
    </form>
</div>

<script src="/assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

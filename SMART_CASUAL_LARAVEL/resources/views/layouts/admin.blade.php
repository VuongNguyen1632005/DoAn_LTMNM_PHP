<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Khu vực quản trị' }} | Smart Casual Admin</title>
    <link rel="stylesheet" href="/assets/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendors/fontawesome/css/all.min.css">
    <style>
        :root {
            --admin-primary: #14221d;
            --admin-secondary: #243b32;
            --admin-accent: #c5a059;
            --admin-light: #f4f6f8;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--admin-light);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-navbar {
            background-color: var(--admin-primary);
            padding: 0.75rem 1.5rem;
            color: #fff;
        }
        .admin-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            letter-spacing: 0.5px;
        }
        .admin-brand span {
            color: var(--admin-accent);
        }
        .admin-sidebar {
            background: #fff;
            border-right: 1px solid #e0e4e8;
            min-height: calc(100vh - 60px);
            padding: 1.25rem 0;
        }
        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #8c9ba5;
            padding: 0.5rem 1.5rem;
            margin-top: 0.5rem;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.5rem;
            color: #495057;
            text-decoration: none;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        .admin-nav-link i {
            width: 1.25rem;
            text-align: center;
            font-size: 1rem;
            color: #6c757d;
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            background-color: #f1f5f3;
            color: var(--admin-secondary);
            border-left-color: var(--admin-accent);
        }
        .admin-nav-link.active i {
            color: var(--admin-secondary);
        }
        .admin-content {
            padding: 2rem;
            flex-grow: 1;
        }
        .card-stat {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s ease;
        }
        .card-stat:hover {
            transform: translateY(-2px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-confirmed { background-color: #cce5ff; color: #004085; }
        .badge-packing { background-color: #e2e3e5; color: #383d41; }
        .badge-shipping { background-color: #d1ecf1; color: #0c5460; }
        .badge-delivered { background-color: #d4edda; color: #155724; }
        .badge-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="admin-navbar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <i class="fa-solid fa-shirt me-2"></i>SMART <span>CASUAL</span> ADMIN
        </a>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 ms-2">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Xem trang web
        </a>
    </div>
    <div class="d-flex align-items-center gap-3">
        @php $admin = Auth::guard('admin')->user(); @endphp
        <span class="text-white-50">
            <i class="fa-solid fa-user-shield me-1"></i>
            {{ $admin->HoTen }} 
            <span class="badge bg-warning text-dark ms-1">{{ strtoupper($admin->ChucVu) }}</span>
        </span>
        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 py-1">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </button>
        </form>
    </div>
</nav>

<div class="container-fluid flex-grow-1">
    <div class="row">
        <aside class="col-md-3 col-lg-2 admin-sidebar d-none d-md-block">
            <div class="sidebar-heading">Tổng quan</div>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Bảng điều khiển
            </a>

            <div class="sidebar-heading">Quản trị danh mục & SP</div>
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Danh mục
            </a>
            <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i> Sản phẩm
            </a>

            <div class="sidebar-heading">Bán hàng & Đơn hàng</div>
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i> Đơn hàng
            </a>
            @if(Auth::guard('admin')->user() && Auth::guard('admin')->user()->isManager())
            <a href="{{ route('admin.revenue.index') }}" class="admin-nav-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Doanh thu
            </a>
            @endif

            <div class="sidebar-heading">Tương tác khách hàng</div>
            <a href="{{ route('admin.reviews.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i class="fa-solid fa-star"></i> Đánh giá
            </a>
        </aside>

        <main class="col-md-9 col-lg-10 admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <h6 class="alert-heading fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Có lỗi nhập liệu:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="/assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

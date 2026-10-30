<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#14221d">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shop thời trang' }} | Smart Casual</title>
    <link rel="stylesheet" href="/assets/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendors/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/catalog.css">
    @stack('styles')
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">S</span>
            <span>SMART<span class="brand-light">CASUAL</span><small>THỜI TRANG MỖI NGÀY</small></span>
        </a>
        <nav class="main-nav" aria-label="Điều hướng chính">
            <a href="{{ route('home') }}">Trang chủ</a>
            <a href="{{ route('about') }}">Giới thiệu</a>
            <a href="{{ route('home') }}#shop-area">Tất cả sản phẩm</a>
            <a href="{{ route('home', ['category' => 'A']) }}#shop-area">Áo</a>
            <a href="{{ route('home', ['category' => 'Q']) }}#shop-area">Quần</a>
            <a href="{{ route('home', ['category' => 'G']) }}#shop-area">Giày</a>
        </nav>
        <div class="header-actions">
            <a class="header-search" href="{{ route('home') }}#shop-area" aria-label="Tìm sản phẩm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>

            @if(Auth::guard('web')->check())
                @php $user = Auth::guard('web')->user(); @endphp
                <a class="account-link" href="{{ route('orders.index') }}">Đơn hàng</a>
                <span class="account-greeting">Xin chào, {{ $user->HoTen }}</span>
                <form class="d-inline" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1 ms-1">Đăng xuất</button>
                </form>
            @elseif(Auth::guard('admin')->check())
                @php $admin = Auth::guard('admin')->user(); @endphp
                <a class="account-link text-warning fw-bold" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-gauge me-1"></i>Vào Quản trị
                </a>
                <span class="account-greeting text-warning">({{ $admin->HoTen }})</span>
            @else
                <a class="account-link" href="{{ route('login') }}">Đăng nhập</a>
                <a class="account-link" href="{{ route('register') }}">Đăng ký</a>
            @endif

            @php
                $cartService = app(\App\Services\CartService::class);
                $cartCount = $cartService->getTotalQuantity();
            @endphp
            <a class="cart-link" href="{{ route('cart.index') }}" aria-label="Giỏ hàng">
                <i class="fa-solid fa-bag-shopping"></i>
                <span>Giỏ hàng</span>
                @if($cartCount > 0)
                    <b>{{ $cartCount }}</b>
                @endif
            </a>
        </div>
    </div>
</header>

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-check me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="container mt-3">
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-info me-2 fs-5"></i>
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<main>
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container d-flex flex-wrap justify-content-between gap-3 align-items-center">
        <span>SMART CASUAL · Thời trang phong cách mỗi ngày.</span>
        <span>Đồ án Lập trình mã nguồn mở · Laravel Framework 11 & MySQL</span>
    </div>
</footer>

<script src="/assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/catalog.js"></script>
@stack('scripts')
</body>
</html>

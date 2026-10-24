@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <span class="eyebrow text-uppercase fw-bold text-muted">VỀ CHÚNG TÔI</span>
            <h1 class="display-5 fw-bold mt-2 mb-3">Thời trang tinh tế cho mỗi ngày của bạn</h1>
            <p class="lead text-secondary mb-4">
                Smart Casual là thương hiệu thời trang tối giản, hiện đại và chỉn chu dành cho những ai tìm kiếm sự tự tin, thoải mái trong công việc và cuộc sống hàng ngày.
            </p>
            <div class="d-flex gap-3">
                <a href="{{ route('home') }}#shop-area" class="btn btn-dark rounded-pill px-4 py-2">Khám phá bộ sưu tập</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row g-3">
                <div class="col-6">
                    <img src="/assets/img/about/about-1-2.jpg" alt="About Smart Casual" class="img-fluid rounded-4 shadow-sm w-100" style="height: 260px; object-fit: cover;">
                </div>
                <div class="col-6">
                    <img src="/assets/img/about/about-1-3.jpg" alt="About Smart Casual" class="img-fluid rounded-4 shadow-sm w-100" style="height: 260px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 text-center mt-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="display-6 text-dark mb-3"><i class="fa-solid fa-gem"></i></div>
                <h5 class="fw-bold">Chất lượng tuyển chọn</h5>
                <p class="text-muted small mb-0">Chất liệu cao cấp từ cotton thoáng mát đến dạ ấm áp, giữ form dáng chuẩn sau nhiều lần giặt.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="display-6 text-dark mb-3"><i class="fa-solid fa-truck-fast"></i></div>
                <h5 class="fw-bold">Giao hàng hỏa tốc</h5>
                <p class="text-muted small mb-0">Hỗ trợ giao hàng hỏa tốc trong 2 giờ tại nội thành và giao nhanh toàn quốc với cước phí tối ưu.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="display-6 text-dark mb-3"><i class="fa-solid fa-arrow-rotate-left"></i></div>
                <h5 class="fw-bold">Đổi trả linh hoạt</h5>
                <p class="text-muted small mb-0">Đổi size hoặc đổi mẫu miễn phí trong vòng 7 ngày nếu không vừa vặn hoặc có lỗi từ nhà sản xuất.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Bảng điều khiển</h2>
        <p class="text-muted mb-0">Thống kê hoạt động kinh doanh và vận hành hệ thống</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-dark rounded-pill px-3">
            <i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Tổng doanh thu</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">{{ number_format($totalRevenue, 0, ',', '.') }}₫</h3>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Tổng đơn hàng</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalOrders }}</h3>
                    <small class="text-warning fw-bold">{{ $pendingOrders }} đơn chờ xử lý</small>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Tổng sản phẩm</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalProducts }}</h3>
                    <small class="text-muted">{{ $totalCategories }} danh mục</small>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-shirt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Khách hàng</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalCustomers }}</h3>
                    <small class="text-success">Thành viên</small>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 ps-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold mb-0">Đơn hàng mới nhất</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $order->MaDonHang }}</td>
                                <td>
                                    <div>{{ $order->TenNguoiNhan }}</div>
                                    <small class="text-muted">{{ $order->SoDienThoai }}</small>
                                </td>
                                <td class="fw-bold text-danger">{{ number_format($order->ThanhTien, 0, ',', '.') }}₫</td>
                                <td>
                                    @php
                                        $badge = match($order->TrangThaiDonHang) {
                                            'pending' => 'badge-pending',
                                            'confirmed' => 'badge-confirmed',
                                            'packing' => 'badge-packing',
                                            'shipping' => 'badge-shipping',
                                            'delivered' => 'badge-delivered',
                                            'cancelled' => 'badge-cancelled',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} rounded-pill px-3 py-1">{{ $order->trang_thai_tieng_viet }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.orders.show', $order->MaDonHang) }}" class="btn btn-sm btn-outline-dark rounded-pill">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Đánh giá mới</h5>
                <a href="{{ route('admin.reviews.index') }}" class="small text-muted text-decoration-none">Tất cả</a>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse($recentReviews as $rev)
                    <div class="border-bottom pb-2">
                        <div class="d-flex justify-content-between">
                            <strong class="small">{{ $rev->TenKhachHang }}</strong>
                            <span class="text-warning small"><i class="fa-solid fa-star"></i> {{ $rev->SoSao }}/5</span>
                        </div>
                        <div class="text-muted small text-truncate">{{ $rev->sanPham->TenSanPham ?? 'Sản phẩm' }}</div>
                        <p class="small text-secondary mb-0 mt-1">{{ Str::limit($rev->NoiDung, 60) }}</p>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Chưa có đánh giá nào.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

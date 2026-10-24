@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Quản lý Đơn hàng</h2>
        <p class="text-muted mb-0">Theo dõi, duyệt đơn và cập nhật trạng thái vận chuyển cho khách</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold" for="search">Tìm kiếm đơn hàng</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control" id="search" name="search" value="{{ $search }}" placeholder="Nhập mã đơn, họ tên hoặc SĐT người nhận...">
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-bold" for="status">Trạng thái xử lý</label>
            <select class="form-select" id="status" name="status">
                <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="confirmed" {{ $selectedStatus === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="packing" {{ $selectedStatus === 'packing' ? 'selected' : '' }}>Đang đóng gói</option>
                <option value="shipping" {{ $selectedStatus === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="delivered" {{ $selectedStatus === 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-dark rounded-pill">
                <i class="fa-solid fa-filter me-1"></i>Lọc danh sách
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Khách hàng / Người nhận</th>
                    <th>Vận chuyển</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái đơn</th>
                    <th>Thanh toán</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $o->MaDonHang }}</td>
                        <td class="text-muted small">
                            {{ $o->NgayDat ? $o->NgayDat->format('d/m/Y H:i') : '' }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $o->TenNguoiNhan }}</div>
                            <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $o->SoDienThoai }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $o->phuongThucVanChuyen->TenPTVC ?? 'HT' }}</span>
                        </td>
                        <td class="fw-bold text-danger">
                            {{ number_format($o->ThanhTien, 0, ',', '.') }}₫
                        </td>
                        <td>
                            @php
                                $badge = match($o->TrangThaiDonHang) {
                                    'pending' => 'badge-pending',
                                    'confirmed' => 'badge-confirmed',
                                    'packing' => 'badge-packing',
                                    'shipping' => 'badge-shipping',
                                    'delivered' => 'badge-delivered',
                                    'cancelled' => 'badge-cancelled',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $badge }} rounded-pill px-3 py-1">{{ $o->trang_thai_tieng_viet }}</span>
                        </td>
                        <td>
                            @if($o->DaThanhToan)
                                <span class="badge bg-success text-white">Đã thu</span>
                            @else
                                <span class="badge bg-warning text-dark">Chưa thu ({{ $o->PhuongThucThanhToan }})</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.orders.show', $o->MaDonHang) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                <i class="fa-solid fa-gear me-1"></i>Xử lý
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt display-4 d-block mb-2"></i>
                            Không tìm thấy đơn hàng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>
@endsection

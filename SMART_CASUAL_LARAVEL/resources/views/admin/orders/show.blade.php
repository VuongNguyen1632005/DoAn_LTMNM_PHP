@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark rounded-pill">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="h3 fw-bold mb-0">Chi tiết đơn hàng #{{ $order->MaDonHang }}</h2>
            <small class="text-muted">Đặt lúc {{ $order->NgayDat ? $order->NgayDat->format('d/m/Y H:i:s') : '' }}</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Order items and summary -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
            <div class="card-header bg-white py-3 ps-4 border-bottom">
                <h5 class="fw-bold mb-0">Các mặt hàng trong đơn ({{ $order->TongSoLuong }} món)</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Sản phẩm</th>
                            <th>Size</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th class="text-end pe-4">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->chiTietDonHangs as $item)
                            @php
                                $sp = $item->chiTietSanPham?->sanPham;
                                $img = $sp?->anhChinh ? '/' . ltrim($sp->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $img }}" alt="{{ $sp->TenSanPham ?? '' }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $sp->TenSanPham ?? 'Sản phẩm' }}</div>
                                            <small class="text-muted">Mã: {{ $item->MaCTSP }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->chiTietSanPham?->kichThuoc?->TenKichThuoc ?? $item->chiTietSanPham?->MaKichThuoc }}</span>
                                </td>
                                <td>{{ number_format($item->DonGia, 0, ',', '.') }}₫</td>
                                <td>× {{ $item->SoLuong }}</td>
                                <td class="text-end pe-4 fw-bold text-danger">{{ number_format($item->ThanhTien, 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Receiver Information -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Thông tin giao nhận & Khách hàng</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tên người nhận</span>
                    <strong class="fs-6">{{ $order->TenNguoiNhan }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Số điện thoại</span>
                    <strong class="fs-6">{{ $order->SoDienThoai }}</strong>
                </div>
                <div class="col-12">
                    <span class="text-muted small d-block">Địa chỉ giao hàng</span>
                    <div>{{ $order->DiaChiGiao }}</div>
                </div>
                @if($order->Email)
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Email</span>
                        <div>{{ $order->Email }}</div>
                    </div>
                @endif
                @if($order->GhiChu)
                    <div class="col-12 border-top pt-2 mt-2">
                        <span class="text-muted small d-block">Ghi chú của khách</span>
                        <div class="fst-italic text-secondary">{{ $order->GhiChu }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Status actions & financial -->
    <div class="col-lg-4">
        <!-- Update Order Status Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Cập nhật trạng thái đơn</h5>

            <form action="{{ route('admin.orders.status', $order->MaDonHang) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="status" class="form-label small fw-bold">Chuyển trạng thái quy trình</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" {{ $order->TrangThaiDonHang === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ $order->TrangThaiDonHang === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="packing" {{ $order->TrangThaiDonHang === 'packing' ? 'selected' : '' }}>Đang đóng gói</option>
                        <option value="shipping" {{ $order->TrangThaiDonHang === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="delivered" {{ $order->TrangThaiDonHang === 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                        <option value="cancelled" {{ $order->TrangThaiDonHang === 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng (Hoàn kho)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label small fw-bold">Ghi chú cập nhật</label>
                    <input type="text" class="form-control" id="note" name="note" placeholder="Ví dụ: Đã đóng gói xong...">
                </div>

                <button type="submit" class="btn btn-dark rounded-pill w-100 py-2 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Lưu trạng thái
                </button>
            </form>
        </div>

        <!-- Payment Status Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Trạng thái thanh toán</h5>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Phương thức:</span>
                <span class="badge bg-light text-dark border fw-bold">{{ $order->PhuongThucThanhToan }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Tình trạng:</span>
                @if($order->DaThanhToan)
                    <span class="badge bg-success">Đã thanh toán</span>
                @else
                    <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                @endif
            </div>

            <form action="{{ route('admin.orders.payment', $order->MaDonHang) }}" method="POST">
                @csrf
                <input type="hidden" name="is_paid" value="{{ $order->DaThanhToan ? 0 : 1 }}">
                @if($order->DaThanhToan)
                    <button type="submit" class="btn btn-outline-secondary rounded-pill w-100 py-2 small">
                        Đánh dấu chưa thanh toán
                    </button>
                @else
                    <button type="submit" class="btn btn-success rounded-pill w-100 py-2 fw-bold">
                        <i class="fa-solid fa-check-double me-1"></i>Xác nhận đã thu tiền
                    </button>
                @endif
            </form>
        </div>

        <!-- Financial Breakdown -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Tổng cộng tài chính</h5>

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Tiền hàng:</span>
                <span>{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Phí ship:</span>
                <span>{{ number_format($order->PhiVanChuyen, 0, ',', '.') }}₫</span>
            </div>

            @if($order->GiamGia > 0)
            <div class="d-flex justify-content-between mb-2 text-success">
                <span>Giảm giá ({{ $order->MaCode }}):</span>
                <span>-{{ number_format($order->GiamGia, 0, ',', '.') }}₫</span>
            </div>
            @endif

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                <span class="fs-4 fw-bold text-danger">{{ number_format($order->ThanhTien, 0, ',', '.') }}₫</span>
            </div>
        </div>
    </div>
</div>
@endsection

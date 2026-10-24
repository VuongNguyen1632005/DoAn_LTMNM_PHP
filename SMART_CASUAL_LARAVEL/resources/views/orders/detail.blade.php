@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-dark rounded-pill">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 fw-bold mb-0">Đơn hàng #{{ $order->MaDonHang }}</h1>
                <small class="text-muted">Đặt lúc {{ $order->NgayDat ? $order->NgayDat->format('d/m/Y H:i') : '' }}</small>
            </div>
        </div>

        <div class="d-flex gap-2">
            @if(in_array($order->TrangThaiDonHang, ['pending', 'confirmed', 'packing']))
                <form action="{{ route('orders.cancel', $order->MaDonHang) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                        <i class="fa-solid fa-ban me-1"></i>Hủy đơn hàng
                    </button>
                </form>
            @endif

            @if($order->TrangThaiDonHang === 'shipping')
                <form action="{{ route('orders.confirm-received', $order->MaDonHang) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-check me-1"></i>Đã nhận được hàng
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    @php
        $statusInfo = match($order->TrangThaiDonHang) {
            'pending' => ['Chờ xác nhận', 'Đơn hàng đã được tiếp nhận và đang chờ cửa hàng xác nhận.', 'warning'],
            'confirmed' => ['Đã xác nhận', 'Đơn hàng đã được xác nhận và chuyển sang bộ phận kho.', 'info'],
            'packing' => ['Đang đóng gói', 'Sản phẩm đang được kiểm tra và đóng gói chỉn chu.', 'secondary'],
            'shipping' => ['Đang giao hàng', 'Đơn hàng đang trên đường vận chuyển tới địa chỉ của bạn.', 'primary'],
            'delivered' => ['Đã giao thành công', 'Đơn hàng đã được giao thành công. Cảm ơn bạn!', 'success'],
            'cancelled' => ['Đã hủy', 'Đơn hàng này đã bị hủy.', 'danger'],
            default => [$order->TrangThaiDonHang, '', 'light']
        };
    @endphp
    <div class="alert alert-{{ $statusInfo[2] }} rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold mb-1"><i class="fa-solid fa-circle-info me-2"></i>Trạng thái: {{ $statusInfo[0] }}</h5>
            <p class="mb-0 small">{{ $statusInfo[1] }}</p>
        </div>
        <span class="fs-4 fw-bold">{{ number_format($order->ThanhTien, 0, ',', '.') }}₫</span>
    </div>

    <div class="row g-4">
        <!-- Purchased Items -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 ps-4 border-bottom">
                    <h5 class="fw-bold mb-0">Danh sách sản phẩm</h5>
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
                                            <img src="{{ $img }}" alt="{{ $sp->TenSanPham ?? '' }}" class="rounded" style="width: 56px; height: 56px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $sp->TenSanPham ?? 'Sản phẩm' }}</div>
                                                <div class="small text-muted">Mã: {{ $item->MaCTSP }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $item->chiTietSanPham?->kichThuoc?->TenKichThuoc ?? $item->chiTietSanPham?->MaKichThuoc }}</span>
                                    </td>
                                    <td>
                                        {{ number_format($item->DonGia, 0, ',', '.') }}₫
                                    </td>
                                    <td>
                                        × {{ $item->SoLuong }}
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-danger">
                                        {{ number_format($item->ThanhTien, 0, ',', '.') }}₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Information Summary -->
        <div class="col-lg-4">
            <!-- Receiver Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Thông tin giao hàng</h5>
                <div class="mb-2">
                    <strong class="text-dark">{{ $order->TenNguoiNhan }}</strong>
                </div>
                <div class="small text-muted mb-2">
                    <i class="fa-solid fa-phone me-1"></i> {{ $order->SoDienThoai }}
                </div>
                @if($order->Email)
                    <div class="small text-muted mb-2">
                        <i class="fa-solid fa-envelope me-1"></i> {{ $order->Email }}
                    </div>
                @endif
                <div class="small text-muted mb-2">
                    <i class="fa-solid fa-location-dot me-1"></i> {{ $order->DiaChiGiao }}
                </div>
                @if($order->GhiChu)
                    <div class="small text-muted border-top pt-2 mt-2">
                        <em>Ghi chú: {{ $order->GhiChu }}</em>
                    </div>
                @endif
            </div>

            <!-- Financial Summary -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Thanh toán</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tổng tiền hàng:</span>
                    <span class="fw-bold">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí vận chuyển ({{ $order->phuongThucVanChuyen->TenPTVC ?? 'HT' }}):</span>
                    <span class="fw-bold">{{ number_format($order->PhiVanChuyen, 0, ',', '.') }}₫</span>
                </div>

                @if($order->GiamGia > 0)
                <div class="d-flex justify-content-between mb-2 text-success fw-bold">
                    <span>Mã giảm giá ({{ $order->MaCode }}):</span>
                    <span>-{{ number_format($order->GiamGia, 0, ',', '.') }}₫</span>
                </div>
                @endif

                <div class="d-flex justify-content-between mb-3 border-top pt-2">
                    <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                    <span class="fs-4 fw-bold text-danger">{{ number_format($order->ThanhTien, 0, ',', '.') }}₫</span>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3 small">
                    <span>Phương thức: <strong>{{ $order->PhuongThucThanhToan }}</strong></span>
                    @if($order->DaThanhToan)
                        <span class="badge bg-success text-white">Đã thanh toán</span>
                    @else
                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

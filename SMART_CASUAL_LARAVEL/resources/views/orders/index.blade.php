@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Lịch sử đơn hàng của bạn</h1>
            <p class="text-muted mb-0">Theo dõi trạng thái và lịch sử mua sắm tại Smart Casual</p>
        </div>
        <a href="{{ route('home') }}#shop-area" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-solid fa-cart-shopping me-2"></i>Mua sắm thêm
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-box-open text-muted display-2 mb-3"></i>
                <h4 class="fw-bold">Bạn chưa có đơn hàng nào</h4>
                <p class="text-muted mb-4">Hãy đặt ngay những món đồ thời trang ưng ý đầu tiên!</p>
                <a href="{{ route('home') }}#shop-area" class="btn btn-dark rounded-pill px-5 py-2">Khám phá ngay</a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái đơn</th>
                            <th>Thanh toán</th>
                            <th class="text-end pe-4">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    {{ $order->MaDonHang }}
                                </td>
                                <td class="text-muted">
                                    {{ $order->NgayDat ? $order->NgayDat->format('d/m/Y H:i') : '' }}
                                </td>
                                <td>
                                    {{ $order->TongSoLuong }} món
                                </td>
                                <td class="text-danger fw-bold">
                                    {{ number_format($order->ThanhTien, 0, ',', '.') }}₫
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($order->TrangThaiDonHang) {
                                            'pending' => 'bg-warning text-dark',
                                            'confirmed' => 'bg-info text-dark',
                                            'packing' => 'bg-secondary text-white',
                                            'shipping' => 'bg-primary text-white',
                                            'delivered' => 'bg-success text-white',
                                            'cancelled' => 'bg-danger text-white',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1">
                                        {{ $order->trang_thai_tieng_viet }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->DaThanhToan)
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Đã thanh toán</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1">Chưa thanh toán ({{ $order->PhuongThucThanhToan }})</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('orders.detail', $order->MaDonHang) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                        Xem <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

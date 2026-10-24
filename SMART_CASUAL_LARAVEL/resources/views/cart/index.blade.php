@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold mb-0">Giỏ hàng của bạn</h1>
        <a href="{{ route('home') }}#shop-area" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i>Tiếp tục mua sắm
        </a>
    </div>

    @if(empty($items))
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-bag-shopping text-muted display-1 mb-3"></i>
                <h3 class="fw-bold">Giỏ hàng đang trống</h3>
                <p class="text-muted mb-4">Bạn chưa chọn sản phẩm nào. Hãy khám phá bộ sưu tập thời trang Smart Casual!</p>
                <a href="{{ route('home') }}#shop-area" class="btn btn-dark rounded-pill px-5 py-2">Khám phá ngay</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Sản phẩm</th>
                                    <th>Size</th>
                                    <th>Đơn giá</th>
                                    <th style="width: 140px;">Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th class="text-end pe-4">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}" class="rounded-3" style="width: 64px; height: 64px; object-fit: cover;">
                                                <div>
                                                    <a href="{{ route('products.detail', $item['product_id']) }}" class="text-decoration-none text-dark fw-bold">
                                                        {{ $item['product_name'] }}
                                                    </a>
                                                    <div class="small text-muted">Mã: {{ $item['variant_id'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $item['size'] }}</span>
                                        </td>
                                        <td class="fw-bold">
                                            {{ number_format($item['price'], 0, ',', '.') }}₫
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="form-control form-control-sm text-center" style="width: 65px;" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="text-danger fw-bold">
                                            {{ number_format($item['line_total'], 0, ',', '.') }}₫
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('cart.remove') }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này?');">
                                                @csrf
                                                <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Xóa">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn làm trống giỏ hàng?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fa-solid fa-trash me-1"></i>Xóa toàn bộ giỏ hàng
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold border-bottom pb-3 mb-3">Tóm tắt đơn hàng</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng số lượng:</span>
                        <span class="fw-bold">{{ $totalQuantity }} sản phẩm</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tạm tính tiền hàng:</span>
                        <span class="fs-5 fw-bold text-dark">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="alert alert-light border small text-muted mb-4">
                        <i class="fa-solid fa-truck-fast me-1 text-success"></i> Phí vận chuyển và mã giảm giá sẽ được áp dụng tại bước thanh toán.
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-dark rounded-pill w-100 py-3 fw-bold fs-6">
                        Tiến hành đặt hàng <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

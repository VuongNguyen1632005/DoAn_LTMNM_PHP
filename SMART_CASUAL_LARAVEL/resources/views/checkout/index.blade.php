@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-dark rounded-pill me-3">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="h2 fw-bold mb-0">Thanh toán & Đặt hàng</h1>
    </div>

    @if(request()->boolean('session_expired'))
        <div class="alert alert-warning" role="alert">
            Phiên thanh toán đã hết hạn. Vui lòng kiểm tra lại thông tin và nhấn đặt hàng lần nữa.
        </div>
    @endif

    <form id="checkoutForm" action="/checkout" method="POST">
        @csrf
        <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
        <div class="row g-4">
            <!-- Left: Shipping & Payment details -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-location-dot me-2 text-danger"></i>Thông tin người nhận hàng</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="receiver_name" class="form-label fw-bold small">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('receiver_name') is-invalid @enderror" id="receiver_name" name="receiver_name" value="{{ old('receiver_name', $customer->HoTen ?? '') }}" required>
                            @error('receiver_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="receiver_phone" class="form-label fw-bold small">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('receiver_phone') is-invalid @enderror" id="receiver_phone" name="receiver_phone" value="{{ old('receiver_phone', $customer->SoDienThoai ?? '') }}" placeholder="0901234567" required>
                            @error('receiver_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="receiver_email" class="form-label fw-bold small">Địa chỉ Email nhận thông báo</label>
                            <input type="email" class="form-control @error('receiver_email') is-invalid @enderror" id="receiver_email" name="receiver_email" value="{{ old('receiver_email', $customer->Email ?? '') }}" placeholder="email@domain.com">
                            @error('receiver_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="shipping_address" class="form-label fw-bold small">Địa chỉ giao hàng chi tiết <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" value="{{ old('shipping_address', $customer->DiaChi ?? '') }}" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành" required>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="note" class="form-label fw-bold small">Ghi chú giao hàng (nếu có)</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ví dụ: Giao vào giờ hành chính, gọi trước khi đến...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method Selection -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-truck-fast me-2 text-primary"></i>Phương thức vận chuyển</h5>
                    <div class="row g-2">
                        @foreach($shipping_methods as $method)
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer w-100 bg-white" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" name="shipping_code" value="{{ $method->MaPTVC }}" {{ (old('shipping_code', $selected_shipping) === $method->MaPTVC) ? 'checked' : '' }} onchange="updateCheckoutSummary()">
                                        <span class="fw-bold">{{ $method->TenPTVC }}</span>
                                    </div>
                                    <span class="text-danger fw-bold">{{ number_format($method->GiaVanChuyen, 0, ',', '.') }}₫</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-credit-card me-2 text-success"></i>Phương thức thanh toán</h5>
                    <div class="d-flex flex-column gap-2">
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer bg-white" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-2">
                                <input type="radio" name="payment_method" value="COD" {{ old('payment_method', 'COD') === 'COD' ? 'checked' : '' }}>
                                <span class="fw-bold">Thanh toán khi nhận hàng (COD)</span>
                            </div>
                            <span class="badge bg-success text-white">Khuyên dùng</span>
                        </label>

                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer bg-white" style="cursor: pointer;">
                            <div class="d-flex align-items-center gap-2">
                                <input type="radio" name="payment_method" value="BANK" {{ old('payment_method') === 'BANK' ? 'checked' : '' }}>
                                <span class="fw-bold">Chuyển khoản ngân hàng (QR Code)</span>
                            </div>
                            <span class="text-muted small">Xác nhận nhanh</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 2rem;">
                    <h5 class="fw-bold border-bottom pb-3 mb-3">Đơn hàng của bạn ({{ count($items) }} món)</h5>

                    <div class="order-items-scroll mb-3" style="max-height: 280px; overflow-y: auto;">
                        @foreach($items as $item)
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold small text-truncate" style="max-width: 180px;">{{ $item['product_name'] }}</div>
                                        <div class="text-muted small">Size: {{ $item['size'] }} × {{ $item['quantity'] }}</div>
                                    </div>
                                </div>
                                <div class="fw-bold text-dark small">
                                    {{ number_format($item['line_total'], 0, ',', '.') }}₫
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Voucher Input -->
                    <div class="mb-3">
                        <label for="voucher_code" class="form-label small fw-bold">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-uppercase" id="voucher_code" name="voucher_code" value="{{ old('voucher_code', $voucher_code) }}" placeholder="Ví dụ: SAVE10">
                            <button type="button" class="btn btn-outline-dark" onclick="applyVoucherCode()">Áp dụng</button>
                        </div>
                        @if($voucher)
                            <small class="text-success fw-bold d-block mt-1">
                                <i class="fa-solid fa-check-circle me-1"></i>Đã áp dụng mã {{ $voucher->MaCode }}: Giảm {{ number_format($discount_amount, 0, ',', '.') }}₫
                            </small>
                        @elseif($voucher_error)
                            <small class="text-danger fw-bold d-block mt-1">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $voucher_error }}
                            </small>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính tiền hàng:</span>
                        <span class="fw-bold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="fw-bold text-dark">{{ number_format($shipping_fee, 0, ',', '.') }}₫</span>
                    </div>

                    @if($discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success fw-bold">
                        <span>Giảm giá khuyến mãi:</span>
                        <span>-{{ number_format($discount_amount, 0, ',', '.') }}₫</span>
                    </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                        <span class="fs-3 fw-bold text-danger">{{ number_format($grand_total, 0, ',', '.') }}₫</span>
                    </div>

                    <button type="submit" id="btnSubmitOrder" class="btn btn-dark rounded-pill w-100 py-3 fw-bold fs-5 shadow">
                        <i class="fa-solid fa-lock me-2"></i>Hoàn tất đặt hàng
                    </button>
                    <small class="text-muted text-center d-block mt-2">Bằng việc đặt hàng, bạn đồng ý với điều khoản của Smart Casual.</small>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let checkoutSubmitting = false;

document.getElementById('checkoutForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    if (checkoutSubmitting) return;
    checkoutSubmitting = true;

    const btn = document.getElementById('btnSubmitOrder');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý đơn hàng...';
    }

    try {
        const response = await fetch(window.location.href, {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!response.ok) throw new Error('Không thể làm mới phiên thanh toán.');

        const html = await response.text();
        const freshPage = new DOMParser().parseFromString(html, 'text/html');
        const freshToken = freshPage.querySelector('#checkoutForm input[name="_token"]')?.value;
        const currentToken = this.querySelector('input[name="_token"]');

        if (!freshToken || !currentToken) throw new Error('Không tìm thấy mã bảo vệ phiên thanh toán.');

        currentToken.value = freshToken;
        this.submit();
    } catch (error) {
        checkoutSubmitting = false;
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i>Hoàn tất đặt hàng';
        }
        window.alert('Không thể xác nhận phiên thanh toán. Vui lòng tải lại trang checkout rồi thử lại.');
    }
});

function updateCheckoutSummary() {
    const shipping = document.querySelector('input[name="shipping_code"]:checked')?.value || 'HT';
    const voucher = document.getElementById('voucher_code').value.trim();
    let url = new URL(window.location.href);
    url.searchParams.set('shipping_code', shipping);
    if (voucher) {
        url.searchParams.set('voucher_code', voucher);
    } else {
        url.searchParams.delete('voucher_code');
    }
    window.location.href = url.toString();
}

function applyVoucherCode() {
    updateCheckoutSummary();
}
</script>
@endpush
@endsection

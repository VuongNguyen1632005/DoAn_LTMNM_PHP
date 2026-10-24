@extends('layouts.app')

@section('content')
<div class="container breadcrumb-line py-3">
    <a href="{{ route('home') }}">Trang chủ</a>
    <i class="fa-solid fa-chevron-right mx-2 text-muted" style="font-size: 0.8rem;"></i>
    <a href="{{ route('home') }}#shop-area">Sản phẩm</a>
    <i class="fa-solid fa-chevron-right mx-2 text-muted" style="font-size: 0.8rem;"></i>
    <span>{{ $product->TenSanPham }}</span>
</div>

<section class="container detail-section py-4">
    <div class="row g-4 g-lg-5">
        <div class="col-lg-6">
            <div class="detail-main-image text-center bg-light rounded-4 p-4 mb-3">
                @php
                    $mainImg = $product->anhChinh ? '/' . ltrim($product->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
                @endphp
                <img id="mainProductImage" src="{{ $mainImg }}" alt="{{ $product->TenSanPham }}" class="img-fluid" style="max-height: 480px; object-fit: contain;">
            </div>

            @if($product->hinhAnhs->count() > 1)
            <div class="detail-thumbnails d-flex gap-2 overflow-auto pb-2" role="list">
                @foreach($product->hinhAnhs as $index => $image)
                    @php $thumb = '/' . ltrim($image->DuongDan, '/'); @endphp
                    <button class="thumb-button btn p-1 border rounded {{ $loop->first ? 'border-dark' : 'border-light' }}" 
                            type="button" 
                            onclick="document.getElementById('mainProductImage').src = '{{ $thumb }}'">
                        <img src="{{ $thumb }}" alt="{{ $product->TenSanPham }}" style="width: 60px; height: 60px; object-fit: cover;">
                    </button>
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-lg-6 detail-copy">
            <span class="eyebrow text-uppercase text-muted fw-bold">{{ $product->danhMuc->TenDanhMuc ?? 'Sản phẩm' }} · {{ $product->MaSP }}</span>
            <h1 class="display-6 fw-bold mt-1 mb-2">{{ $product->TenSanPham }}</h1>

            <div class="rating-line detail-rating d-flex align-items-center gap-2 mb-3">
                <span class="stars text-warning fw-bold">
                    <i class="fa-solid fa-star"></i> {{ number_format($product->diem_danh_gia_trung_binh, 1, ',', '.') }}
                </span>
                <a href="#review-area" class="text-decoration-none text-muted">({{ $product->so_luong_danh_gia }} đánh giá)</a>
            </div>

            <div class="detail-price fs-2 fw-bold text-danger mb-3">
                {{ number_format($product->gia_hien_thi, 0, ',', '.') }}₫
            </div>

            <p class="detail-description text-secondary mb-4" style="line-height: 1.6;">
                {!! nl2br(e($product->MoTa ?: 'Chất liệu cao cấp, phom dáng chuẩn, đường may sắc nét mang đến sự thoải mái và tự tin suốt cả ngày.')) !!}
            </p>

            <div class="variant-heading fw-bold mb-2">Kích thước có sẵn</div>
            <div class="variant-list d-flex flex-wrap gap-2 mb-4">
                @forelse($product->chiTietSanPhams as $variant)
                    <div class="variant-chip border rounded-3 p-2 px-3 text-center bg-white shadow-sm">
                        <strong>Size {{ $variant->kichThuoc->TenKichThuoc ?? $variant->MaKichThuoc }}</strong>
                        <div class="text-danger small">{{ number_format($variant->GiaBan, 0, ',', '.') }}₫</div>
                        @if($variant->SoLuong < 1)
                            <small class="text-danger">Hết hàng</small>
                        @else
                            <small class="text-muted">Còn {{ $variant->SoLuong }}</small>
                        @endif
                    </div>
                @empty
                    <span class="text-muted">Hiện chưa có biến thể size.</span>
                @endforelse
            </div>

            @php
                $availableVariants = $product->chiTietSanPhams->where('SoLuong', '>', 0);
            @endphp

            @if($availableVariants->isNotEmpty())
            <form class="add-to-cart-form mt-4" action="{{ route('cart.add') }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-sm-6">
                        <label class="form-label fw-bold" for="variant_id">Chọn kích thước</label>
                        <select class="form-select size-select" id="variant_id" name="variant_id" required>
                            @foreach($availableVariants as $variant)
                                <option value="{{ $variant->MaCTSP }}">
                                    Size {{ $variant->kichThuoc->TenKichThuoc ?? $variant->MaKichThuoc }} · {{ number_format($variant->GiaBan, 0, ',', '.') }}₫ (Còn {{ $variant->SoLuong }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label class="form-label fw-bold" for="quantity">Số lượng</label>
                        <input class="form-control text-center" id="quantity" type="number" name="quantity" value="1" min="1" max="50" required>
                    </div>

                    <div class="col-sm-3 d-grid">
                        <button class="btn btn-dark rounded-pill py-2" type="submit">
                            <i class="fa-solid fa-bag-shopping me-2"></i>Thêm giỏ
                        </button>
                    </div>
                </div>
            </form>
            @else
                <div class="alert alert-secondary mt-4">Sản phẩm hiện đã hết hàng.</div>
            @endif

            <div class="detail-note d-flex align-items-center gap-2 text-muted small mt-4 pt-3 border-top">
                <i class="fa-solid fa-shield-halved text-success"></i>
                <span>Sản phẩm chính hãng Smart Casual. Cam kết chất lượng và đổi trả trong 7 ngày.</span>
            </div>
        </div>
    </div>
</section>

<!-- Review section -->
<section id="review-area" class="review-section bg-light py-5 mt-5">
    <div class="container">
        <div class="section-heading d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="eyebrow text-uppercase text-muted fw-bold">CHIA SẺ TỪ KHÁCH HÀNG</span>
                <h2 class="fw-bold">Đánh giá sản phẩm</h2>
            </div>
            <span class="review-summary fs-5 text-warning fw-bold">
                <i class="fa-solid fa-star"></i> {{ number_format($product->diem_danh_gia_trung_binh, 1, ',', '.') }} / 5 · {{ $reviews->count() }} đánh giá
            </span>
        </div>

        @if(session('review_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('review_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3">Gửi đánh giá của bạn</h5>
                    <form action="{{ route('products.reviews.submit', $product->MaSP) }}" method="POST">
                        @csrf
                        @if(Auth::guard('web')->check())
                            <div class="mb-3">
                                <label class="form-label text-muted small">Người đánh giá</label>
                                <input type="text" class="form-control" name="reviewer_name" value="{{ Auth::guard('web')->user()->HoTen }}" readonly>
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="reviewerName" class="form-label text-muted small">Họ tên của bạn</label>
                                <input class="form-control" id="reviewerName" name="reviewer_name" value="{{ old('reviewer_name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="reviewRating" class="form-label text-muted small">Số sao đánh giá</label>
                            <select class="form-select" id="reviewRating" name="rating" required>
                                <option value="5" selected>5 sao (Rất hài lòng)</option>
                                <option value="4">4 sao (Hài lòng)</option>
                                <option value="3">3 sao (Bình thường)</option>
                                <option value="2">2 sao (Chưa hài lòng)</option>
                                <option value="1">1 sao (Kém)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reviewContent" class="form-label text-muted small">Nội dung nhận xét</label>
                            <textarea class="form-control" id="reviewContent" name="content" rows="4" placeholder="Chia sẻ cảm nhận về chất liệu, kích cỡ, trải nghiệm mặc..." required>{{ old('content') }}</textarea>
                        </div>

                        <button class="btn btn-dark rounded-pill w-100 py-2" type="submit">Gửi nhận xét</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                @if($reviews->isEmpty())
                    <p class="text-muted py-4">Sản phẩm này hiện chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ cảm nhận!</p>
                @else
                    <div class="row g-3">
                        @foreach($reviews as $review)
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-dark">{{ $review->TenKhachHang }}</strong>
                                        <span class="text-warning small">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="fa-{{ $s <= $review->SoSao ? 'solid' : 'regular' }} fa-star"></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <p class="text-secondary small mb-0">{{ $review->NoiDung }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if($related->isNotEmpty())
<section class="container section-block py-5">
    <div class="section-heading mb-4">
        <div>
            <span class="eyebrow text-uppercase text-muted fw-bold">CÙNG DANH MỤC</span>
            <h2 class="fw-bold">Sản phẩm liên quan</h2>
        </div>
    </div>
    <div class="row g-3 g-lg-4">
        @foreach($related as $item)
            <div class="col-6 col-md-3">
                @include('catalog.product-card', ['item' => $item])
            </div>
        @endforeach
    </div>
</section>
@endif
@endsection

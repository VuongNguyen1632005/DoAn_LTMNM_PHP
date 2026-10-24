@php
    $imgSrc = $item->anhChinh ? '/' . ltrim($item->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
    $priceFormatted = number_format($item->gia_hien_thi, 0, ',', '.') . '₫';
@endphp
<article class="product-card">
    <a class="product-image" href="{{ route('products.detail', $item->MaSP) }}">
        <img loading="lazy" src="{{ $imgSrc }}" alt="{{ $item->TenSanPham }}">
        <span class="image-arrow"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
    </a>
    <div class="product-info">
        <div class="rating-line">
            <span class="stars"><i class="fa-solid fa-star"></i> {{ number_format($item->diem_danh_gia_trung_binh, 1, ',', '.') }}</span>
            <span>({{ $item->so_luong_danh_gia }})</span>
        </div>
        <a class="product-name" href="{{ route('products.detail', $item->MaSP) }}">{{ $item->TenSanPham }}</a>
        <div class="product-bottom">
            <strong>{{ $priceFormatted }}</strong>
            <span>{{ $item->danhMuc->TenDanhMuc ?? '' }}</span>
        </div>
    </div>
</article>

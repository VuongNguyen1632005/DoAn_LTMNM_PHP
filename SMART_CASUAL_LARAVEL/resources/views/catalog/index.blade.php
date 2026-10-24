@extends('layouts.app')

@section('content')
<section class="hero-band">
    <div class="container hero-content">
        <div>
            <span class="eyebrow">BỘ SƯU TẬP MỚI</span>
            <h1>Mặc đẹp theo<br><em>cách của bạn.</em></h1>
            <p>Những lựa chọn thoải mái, chỉn chu cho mọi ngày.</p>
            <a class="btn btn-light rounded-pill px-4 py-2" href="#shop-area">
                Khám phá sản phẩm <i class="fa-solid fa-arrow-down ms-2"></i>
            </a>
        </div>
        <div class="hero-art">
            <img src="/assets/img/banner/Banner-1.jpg" alt="Bộ sưu tập thời trang Smart Casual">
        </div>
    </div>
</section>

@if($trending->isNotEmpty())
<section class="container section-block">
    <div class="section-heading">
        <div>
            <span class="eyebrow">ĐƯỢC YÊU THÍCH</span>
            <h2>Trang phục thịnh hành</h2>
        </div>
        <a href="#shop-area" class="text-link">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-3 g-lg-4">
        @foreach($trending as $item)
            <div class="col-6 col-lg-3">
                @include('catalog.product-card', ['item' => $item])
            </div>
        @endforeach
    </div>
</section>
@endif

<section id="shop-area" class="container section-block catalog-section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">TÌM PHONG CÁCH CỦA BẠN</span>
            <h2>Tất cả sản phẩm</h2>
            <p class="muted mb-0">Tìm thấy {{ $total }} sản phẩm</p>
        </div>
    </div>

    <form class="filter-panel row g-2 align-items-end" action="{{ route('home') }}" method="GET">
        <div class="col-12 col-md-4">
            <label for="searchName">Tìm theo tên sản phẩm</label>
            <div class="input-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input id="searchName" class="form-control" name="searchName" value="{{ $search }}" placeholder="Ví dụ: Áo polo, Quần tây...">
            </div>
        </div>

        <div class="col-6 col-md-2">
            <label for="category">Danh mục</label>
            <select id="category" class="form-select" name="category">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->MaDanhMuc }}" {{ $category === $cat->MaDanhMuc ? 'selected' : '' }}>
                        {{ $cat->TenDanhMuc }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label for="priceRange">Khoảng giá</label>
            <select id="priceRange" class="form-select" name="priceRange">
                <option value="">Tất cả mức giá</option>
                <option value="0-300000" {{ $priceRange === '0-300000' ? 'selected' : '' }}>Dưới 300.000₫</option>
                <option value="300000-600000" {{ $priceRange === '300000-600000' ? 'selected' : '' }}>300.000₫ – 600.000₫</option>
                <option value="600000-1000000" {{ $priceRange === '600000-1000000' ? 'selected' : '' }}>600.000₫ – 1.000.000₫</option>
                <option value="1000000-999999999" {{ $priceRange === '1000000-999999999' ? 'selected' : '' }}>Trên 1.000.000₫</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label for="sortType">Sắp xếp</label>
            <select id="sortType" class="form-select" name="sortType">
                <option value="default" {{ $sort === 'default' ? 'selected' : '' }}>Mặc định</option>
                <option value="asc" {{ $sort === 'asc' ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="desc" {{ $sort === 'desc' ? 'selected' : '' }}>Giá giảm dần</option>
            </select>
        </div>

        <div class="col-6 col-md-2 d-grid">
            <button class="btn btn-dark rounded-pill" type="submit">
                <i class="fa-solid fa-sliders me-2"></i>Lọc sản phẩm
            </button>
        </div>
    </form>

    @if($products->isEmpty())
        <div class="empty-state text-center py-5">
            <i class="fa-regular fa-face-frown fs-1 text-muted mb-3"></i>
            <h3>Chưa tìm thấy sản phẩm phù hợp</h3>
            <p class="text-muted">Thử thay đổi từ khóa hoặc điều kiện bộ lọc của bạn.</p>
            <a class="btn btn-outline-dark rounded-pill px-4" href="{{ route('home') }}#shop-area">Xóa bộ lọc</a>
        </div>
    @else
        <div class="row g-3 g-lg-4 product-grid mt-2">
            @foreach($products as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('catalog.product-card', ['item' => $item])
                </div>
            @endforeach
        </div>

        <div class="pagination-wrap mt-5 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</section>
@endsection

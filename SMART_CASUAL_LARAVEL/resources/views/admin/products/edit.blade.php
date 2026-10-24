@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-dark rounded-pill me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="h3 fw-bold mb-0">Chỉnh sửa sản phẩm: {{ $product->TenSanPham }}</h2>
        <small class="text-muted">Mã: {{ $product->MaSP }}</small>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('admin.products.update', $product->MaSP) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Mã sản phẩm (Không thể thay đổi)</label>
                        <input type="text" class="form-control bg-light" value="{{ $product->MaSP }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="MaDanhMuc" class="form-label small fw-bold">Danh mục sản phẩm <span class="text-danger">*</span></label>
                        <select class="form-select @error('MaDanhMuc') is-invalid @enderror" id="MaDanhMuc" name="MaDanhMuc" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->MaDanhMuc }}" {{ old('MaDanhMuc', $product->MaDanhMuc) === $cat->MaDanhMuc ? 'selected' : '' }}>
                                    {{ $cat->TenDanhMuc }} ({{ $cat->MaDanhMuc }})
                                </option>
                            @endforeach
                        </select>
                        @error('MaDanhMuc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="TenSanPham" class="form-label small fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('TenSanPham') is-invalid @enderror" id="TenSanPham" name="TenSanPham" value="{{ old('TenSanPham', $product->TenSanPham) }}" required>
                        @error('TenSanPham')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="GiaBan" class="form-label small fw-bold">Giá bán hiện tại (VND) <span class="text-danger">*</span></label>
                        <input type="number" step="1000" class="form-control @error('GiaBan') is-invalid @enderror" id="GiaBan" name="GiaBan" value="{{ old('GiaBan', (int) ($primaryVariant->GiaBan ?? $product->gia_hien_thi)) }}" required>
                        @error('GiaBan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="SoLuong" class="form-label small fw-bold">Số lượng tồn kho (Size chính) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('SoLuong') is-invalid @enderror" id="SoLuong" name="SoLuong" value="{{ old('SoLuong', $primaryVariant->SoLuong ?? 0) }}" min="0" required>
                        @error('SoLuong')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="image" class="form-label small fw-bold">Thay đổi ảnh đại diện (Tùy chọn)</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @php
                                $currentImg = $product->anhChinh ? '/' . ltrim($product->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
                            @endphp
                            <img src="{{ $currentImg }}" alt="Current Image" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                <small class="text-muted">Chọn file mới nếu muốn thay thế ảnh hiện tại.</small>
                            </div>
                        </div>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="MoTa" class="form-label small fw-bold">Mô tả sản phẩm</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="4">{{ old('MoTa', $product->MoTa) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">
                        <i class="fa-solid fa-check me-1"></i>Cập nhật sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

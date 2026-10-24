@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-dark rounded-pill me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h2 class="h3 fw-bold mb-0">Thêm sản phẩm mới</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="MaSP" class="form-label small fw-bold">Mã sản phẩm (duy nhất) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase @error('MaSP') is-invalid @enderror" id="MaSP" name="MaSP" value="{{ old('MaSP') }}" placeholder="Ví dụ: A25, Q13..." required>
                        @error('MaSP')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="MaDanhMuc" class="form-label small fw-bold">Danh mục sản phẩm <span class="text-danger">*</span></label>
                        <select class="form-select @error('MaDanhMuc') is-invalid @enderror" id="MaDanhMuc" name="MaDanhMuc" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->MaDanhMuc }}" {{ old('MaDanhMuc') === $cat->MaDanhMuc ? 'selected' : '' }}>
                                    {{ $cat->TenDanhMuc }} ({{ $cat->MaDanhMuc }})
                                </option>
                            @endforeach
                        </select>
                        @error('MaDanhMuc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="TenSanPham" class="form-label small fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('TenSanPham') is-invalid @enderror" id="TenSanPham" name="TenSanPham" value="{{ old('TenSanPham') }}" placeholder="Ví dụ: Áo Sơ Mi Oxford Dài Tay" required>
                        @error('TenSanPham')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="GiaBan" class="form-label small fw-bold">Giá bán (VND) <span class="text-danger">*</span></label>
                        <input type="number" step="1000" class="form-control @error('GiaBan') is-invalid @enderror" id="GiaBan" name="GiaBan" value="{{ old('GiaBan', 250000) }}" required>
                        @error('GiaBan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="SoLuong" class="form-label small fw-bold">Số lượng tồn kho ban đầu <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('SoLuong') is-invalid @enderror" id="SoLuong" name="SoLuong" value="{{ old('SoLuong', 50) }}" min="0" required>
                        @error('SoLuong')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="MaKichThuoc" class="form-label small fw-bold">Size khởi tạo <span class="text-danger">*</span></label>
                        <select class="form-select @error('MaKichThuoc') is-invalid @enderror" id="MaKichThuoc" name="MaKichThuoc" required>
                            @foreach($sizes as $s)
                                <option value="{{ $s->MaKichThuoc }}" {{ old('MaKichThuoc') === $s->MaKichThuoc ? 'selected' : '' }}>
                                    Size {{ $s->TenKichThuoc }}
                                </option>
                            @endforeach
                        </select>
                        @error('MaKichThuoc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="image" class="form-label small fw-bold">Ảnh đại diện sản phẩm (Upload file)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        <small class="text-muted">Hỗ trợ JPG, PNG, WEBP tối đa 4MB.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="MoTa" class="form-label small fw-bold">Mô tả chi tiết sản phẩm</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="4" placeholder="Nhập chất liệu, kiểu dáng, hướng dẫn bảo quản...">{{ old('MoTa') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

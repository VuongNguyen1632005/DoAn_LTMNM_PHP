@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-dark rounded-pill me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h2 class="h3 fw-bold mb-0">Thêm danh mục mới</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="MaDanhMuc" class="form-label small fw-bold">Mã danh mục (Viết hoa, tối đa 10 ký tự) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase @error('MaDanhMuc') is-invalid @enderror" id="MaDanhMuc" name="MaDanhMuc" value="{{ old('MaDanhMuc') }}" placeholder="Ví dụ: PK, V..." required>
                    @error('MaDanhMuc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="TenDanhMuc" class="form-label small fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('TenDanhMuc') is-invalid @enderror" id="TenDanhMuc" name="TenDanhMuc" value="{{ old('TenDanhMuc') }}" placeholder="Ví dụ: Phụ Kiện Thời Trang" required>
                    @error('TenDanhMuc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

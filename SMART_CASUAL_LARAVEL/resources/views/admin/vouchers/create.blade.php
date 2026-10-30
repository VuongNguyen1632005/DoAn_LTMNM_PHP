@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i>Danh sách mã giảm giá</a>
    <h2 class="h3 fw-bold mt-2 mb-1">Tạo mã giảm giá</h2>
    <p class="text-muted mb-0">Mã mới sẽ được bật ngay sau khi tạo.</p>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="max-width: 760px">
    <form action="{{ route('admin.vouchers.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="MaCode" class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                <input id="MaCode" name="MaCode" value="{{ old('MaCode') }}" class="form-control text-uppercase @error('MaCode') is-invalid @enderror" required maxlength="50" autocomplete="off" placeholder="VD: SUMMER10">
                @error('MaCode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="LoaiGiamGia" class="form-label fw-semibold">Loại ưu đãi <span class="text-danger">*</span></label>
                <select id="LoaiGiamGia" name="LoaiGiamGia" class="form-select @error('LoaiGiamGia') is-invalid @enderror" required>
                    <option value="cash" @selected(old('LoaiGiamGia', 'cash') === 'cash')>Giảm tiền mặt</option>
                    <option value="percent" @selected(old('LoaiGiamGia') === 'percent')>Giảm theo phần trăm</option>
                    <option value="shipping" @selected(old('LoaiGiamGia') === 'shipping')>Giảm phí vận chuyển</option>
                </select>
                @error('LoaiGiamGia')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="GiaTri" class="form-label fw-semibold">Giá trị giảm <span class="text-danger">*</span></label>
                <input id="GiaTri" name="GiaTri" type="number" step="0.01" min="0" value="{{ old('GiaTri') }}" class="form-control @error('GiaTri') is-invalid @enderror" required>
                @error('GiaTri')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="SoLuong" class="form-label fw-semibold">Số lượt sử dụng <span class="text-danger">*</span></label>
                <input id="SoLuong" name="SoLuong" type="number" min="0" step="1" value="{{ old('SoLuong', 10) }}" class="form-control @error('SoLuong') is-invalid @enderror" required>
                @error('SoLuong')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="alert alert-info mt-3 mb-0 small">
            <strong>Cách tính:</strong> Tiền mặt được trừ vào tiền hàng; phần trăm áp dụng trên tiền hàng và tối đa 100%; phí vận chuyển nhập 0 để miễn toàn bộ, hoặc nhập mức giảm tối đa.
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-dark rounded-pill px-4" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Tạo mã</button>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
        </div>
    </form>
</div>
@endsection

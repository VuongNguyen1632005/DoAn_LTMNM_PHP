@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i>Danh sách mã giảm giá</a>
    <h2 class="h3 fw-bold mt-2 mb-1">Cập nhật mã giảm giá</h2>
    <p class="text-muted mb-0">Mã: <strong class="text-primary">{{ $voucher->MaCode }}</strong></p>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="max-width: 760px">
    <form action="{{ route('admin.vouchers.update', $voucher->MaCode) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label for="LoaiGiamGia" class="form-label fw-semibold">Loại ưu đãi <span class="text-danger">*</span></label>
                <select id="LoaiGiamGia" name="LoaiGiamGia" class="form-select @error('LoaiGiamGia') is-invalid @enderror" required>
                    <option value="cash" @selected(old('LoaiGiamGia', $voucher->LoaiGiamGia) === 'cash')>Giảm tiền mặt</option>
                    <option value="percent" @selected(old('LoaiGiamGia', $voucher->LoaiGiamGia) === 'percent')>Giảm theo phần trăm</option>
                    <option value="shipping" @selected(old('LoaiGiamGia', $voucher->LoaiGiamGia) === 'shipping')>Giảm phí vận chuyển</option>
                </select>
                @error('LoaiGiamGia')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="GiaTri" class="form-label fw-semibold">Giá trị giảm <span class="text-danger">*</span></label>
                <input id="GiaTri" name="GiaTri" type="number" step="0.01" min="0" value="{{ old('GiaTri', $voucher->GiaTri) }}" class="form-control @error('GiaTri') is-invalid @enderror" required>
                @error('GiaTri')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="SoLuong" class="form-label fw-semibold">Số lượt còn lại <span class="text-danger">*</span></label>
                <input id="SoLuong" name="SoLuong" type="number" min="0" step="1" value="{{ old('SoLuong', $voucher->SoLuong) }}" class="form-control @error('SoLuong') is-invalid @enderror" required>
                @error('SoLuong')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="TrangThai" class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                <select id="TrangThai" name="TrangThai" class="form-select @error('TrangThai') is-invalid @enderror" required>
                    <option value="1" @selected((string) old('TrangThai', (int) $voucher->TrangThai) === '1')>Đang bật</option>
                    <option value="0" @selected((string) old('TrangThai', (int) $voucher->TrangThai) === '0')>Đang tắt</option>
                </select>
                @error('TrangThai')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="alert alert-info mt-3 mb-0 small">
            Mã giảm giá không đổi để giữ liên kết với các đơn hàng đã sử dụng. Tiền mặt được trừ vào tiền hàng; phần trăm tối đa 100%; phí vận chuyển nhập 0 để miễn toàn bộ.
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-dark rounded-pill px-4" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu thay đổi</button>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
        </div>
    </form>
</div>
@endsection

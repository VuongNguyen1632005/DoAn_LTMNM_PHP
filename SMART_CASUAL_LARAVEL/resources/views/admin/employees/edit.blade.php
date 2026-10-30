@extends('layouts.admin')

@section('content')
@php $isSelf = $employee->MaNV === Auth::guard('admin')->user()->MaNV; @endphp
<div class="mb-4">
    <a href="{{ route('admin.employees.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i>Danh sách nhân viên</a>
    <h2 class="h3 fw-bold mt-2 mb-1">Cập nhật nhân viên</h2>
    <p class="text-muted mb-0">Mã nhân viên: <strong>{{ $employee->MaNV }}</strong></p>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="max-width: 760px">
    <form action="{{ route('admin.employees.update', $employee->MaNV) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label for="HoTen" class="form-label fw-semibold">Họ tên</label>
                <input id="HoTen" name="HoTen" value="{{ old('HoTen', $employee->HoTen) }}" class="form-control @error('HoTen') is-invalid @enderror" required maxlength="100">
                @error('HoTen')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="TenDangNhap" class="form-label fw-semibold">Tên đăng nhập</label>
                <input id="TenDangNhap" name="TenDangNhap" value="{{ old('TenDangNhap', $employee->TenDangNhap) }}" class="form-control @error('TenDangNhap') is-invalid @enderror" required maxlength="100" autocomplete="off">
                @error('TenDangNhap')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="ChucVu" class="form-label fw-semibold">Vai trò</label>
                @if($isSelf)
                    <input type="hidden" name="ChucVu" value="manager">
                    <select id="ChucVu" class="form-select" disabled><option>Quản lý (Manager)</option></select>
                    <div class="form-text">Không thể tự hạ quyền tài khoản hiện tại.</div>
                @else
                    <select id="ChucVu" name="ChucVu" class="form-select @error('ChucVu') is-invalid @enderror" required>
                        <option value="staff" @selected(old('ChucVu', $employee->ChucVu) === 'staff')>Nhân viên (Staff)</option>
                        <option value="manager" @selected(old('ChucVu', $employee->ChucVu) === 'manager')>Quản lý (Manager)</option>
                    </select>
                    @error('ChucVu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @endif
            </div>
            <div class="col-md-6">
                <label for="TrangThai" class="form-label fw-semibold">Trạng thái</label>
                @if($isSelf)
                    <input type="hidden" name="TrangThai" value="1">
                    <select id="TrangThai" class="form-select" disabled><option>Đang hoạt động</option></select>
                @else
                    <select id="TrangThai" name="TrangThai" class="form-select @error('TrangThai') is-invalid @enderror" required>
                        <option value="1" @selected((string) old('TrangThai', (int) $employee->TrangThai) === '1')>Đang hoạt động</option>
                        <option value="0" @selected((string) old('TrangThai', (int) $employee->TrangThai) === '0')>Đã khóa</option>
                    </select>
                    @error('TrangThai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @endif
            </div>
            <div class="col-12"><hr><h5 class="fw-bold">Đặt lại mật khẩu <span class="text-muted fw-normal small">(để trống nếu không đổi)</span></h5></div>
            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold">Mật khẩu mới</label>
                <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" minlength="8" autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" minlength="8" autocomplete="new-password">
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-dark rounded-pill px-4" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu thay đổi</button>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
        </div>
    </form>
</div>
@endsection

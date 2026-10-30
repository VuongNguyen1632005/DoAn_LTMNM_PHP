@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.employees.index') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i>Danh sách nhân viên</a>
    <h2 class="h3 fw-bold mt-2 mb-1">Thêm nhân viên</h2>
    <p class="text-muted mb-0">Tạo tài khoản Staff hoặc Manager cho khu vực quản trị.</p>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="max-width: 760px">
    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="HoTen" class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                <input id="HoTen" name="HoTen" value="{{ old('HoTen') }}" class="form-control @error('HoTen') is-invalid @enderror" required maxlength="100">
                @error('HoTen')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="TenDangNhap" class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                <input id="TenDangNhap" name="TenDangNhap" value="{{ old('TenDangNhap') }}" class="form-control @error('TenDangNhap') is-invalid @enderror" required maxlength="100" autocomplete="off">
                @error('TenDangNhap')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="ChucVu" class="form-label fw-semibold">Vai trò <span class="text-danger">*</span></label>
                <select id="ChucVu" name="ChucVu" class="form-select @error('ChucVu') is-invalid @enderror" required>
                    <option value="staff" @selected(old('ChucVu', 'staff') === 'staff')>Nhân viên (Staff)</option>
                    <option value="manager" @selected(old('ChucVu') === 'manager')>Quản lý (Manager)</option>
                </select>
                @error('ChucVu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Tài khoản Manager có thể truy cập các chức năng quản trị nâng cao.</div>
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold">Mật khẩu ban đầu <span class="text-danger">*</span></label>
                <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required minlength="8" autocomplete="new-password">
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-dark rounded-pill px-4" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Tạo tài khoản</button>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
        </div>
    </form>
</div>
@endsection

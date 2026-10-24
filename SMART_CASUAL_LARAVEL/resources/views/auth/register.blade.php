@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill text-uppercase mb-2">Đăng ký mới</span>
                    <h2 class="fw-bold">Tạo tài khoản</h2>
                    <p class="text-muted small">Trở thành thành viên để nhận nhiều ưu đãi thời trang hấp dẫn</p>
                </div>

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="HoTen" class="form-label fw-bold small">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('HoTen') is-invalid @enderror" id="HoTen" name="HoTen" value="{{ old('HoTen') }}" placeholder="Ví dụ: Nguyễn Văn An" required>
                            @error('HoTen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="Email" class="form-label fw-bold small">Địa chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('Email') is-invalid @enderror" id="Email" name="Email" value="{{ old('Email') }}" placeholder="an.nguyen@gmail.com" required>
                            @error('Email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="TenDangNhap" class="form-label fw-bold small">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('TenDangNhap') is-invalid @enderror" id="TenDangNhap" name="TenDangNhap" value="{{ old('TenDangNhap') }}" placeholder="annguyen123" required>
                            @error('TenDangNhap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="MatKhau" class="form-label fw-bold small">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('MatKhau') is-invalid @enderror" id="MatKhau" name="MatKhau" placeholder="Tối thiểu 6 ký tự" required>
                            @error('MatKhau')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="MatKhau_confirmation" class="form-label fw-bold small">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="MatKhau_confirmation" name="MatKhau_confirmation" placeholder="Nhập lại mật khẩu" required>
                        </div>

                        <div class="col-12">
                            <label for="SoDienThoai" class="form-label fw-bold small">Số điện thoại</label>
                            <input type="tel" class="form-control @error('SoDienThoai') is-invalid @enderror" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai') }}" placeholder="0901234567">
                            @error('SoDienThoai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="DiaChi" class="form-label fw-bold small">Địa chỉ</label>
                            <input type="text" class="form-control @error('DiaChi') is-invalid @enderror" id="DiaChi" name="DiaChi" value="{{ old('DiaChi') }}" placeholder="Địa chỉ thường trú">
                            @error('DiaChi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark rounded-pill w-100 py-3 fw-bold mt-4 shadow">
                        Hoàn tất đăng ký
                    </button>

                    <div class="text-center small text-muted mt-3">
                        Đã có tài khoản? <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

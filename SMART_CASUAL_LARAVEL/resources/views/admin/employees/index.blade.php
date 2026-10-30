@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <h2 class="h3 fw-bold mb-1">Quản lý nhân viên</h2>
        <p class="text-muted mb-0">Tạo và quản lý quyền truy cập các tài khoản quản trị</p>
    </div>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-dark rounded-pill px-4">
        <i class="fa-solid fa-user-plus me-1"></i>Thêm nhân viên
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="{{ route('admin.employees.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="search" class="form-label small fw-bold">Tìm nhân viên</label>
            <input id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Mã, họ tên hoặc tên đăng nhập">
        </div>
        <div class="col-md-3">
            <label for="role" class="form-label small fw-bold">Vai trò</label>
            <select id="role" name="role" class="form-select">
                <option value="all" @selected($role === 'all')>Tất cả</option>
                <option value="staff" @selected($role === 'staff')>Nhân viên</option>
                <option value="manager" @selected($role === 'manager')>Quản lý</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label small fw-bold">Trạng thái</label>
            <select id="status" name="status" class="form-select">
                <option value="all" @selected($status === 'all')>Tất cả</option>
                <option value="active" @selected($status === 'active')>Đang hoạt động</option>
                <option value="inactive" @selected($status === 'inactive')>Đã khóa</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-dark rounded-pill" type="submit"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Mã nhân viên</th>
                    <th>Họ tên / Tên đăng nhập</th>
                    <th>Vai trò</th>
                    <th>Đơn được xử lý</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $employee->MaNV }}</td>
                        <td><div class="fw-semibold">{{ $employee->HoTen }}</div><small class="text-muted">{{ $employee->TenDangNhap }}</small></td>
                        <td><span class="badge {{ $employee->ChucVu === 'manager' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ $employee->ChucVu === 'manager' ? 'Quản lý' : 'Nhân viên' }}</span></td>
                        <td>{{ $employee->don_hangs_count }}</td>
                        <td>
                            <span class="badge {{ $employee->TrangThai ? 'bg-success-subtle text-success border border-success' : 'bg-secondary-subtle text-secondary border' }}">
                                {{ $employee->TrangThai ? 'Đang hoạt động' : 'Đã khóa' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.employees.edit', $employee->MaNV) }}" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-pen-to-square me-1"></i>Sửa</a>
                                @if($employee->MaNV !== $currentEmployeeId)
                                    <form action="{{ route('admin.employees.status', $employee->MaNV) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn {{ $employee->TrangThai ? 'khóa' : 'mở khóa' }} tài khoản này?');">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm {{ $employee->TrangThai ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit">
                                            {{ $employee->TrangThai ? 'Khóa' : 'Mở khóa' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted align-self-center">Tài khoản của bạn</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Không tìm thấy nhân viên.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">{{ $employees->links('pagination::bootstrap-5') }}</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <h2 class="h3 fw-bold mb-1">Quản lý mã giảm giá</h2>
        <p class="text-muted mb-0">Theo dõi loại ưu đãi, giá trị giảm và số lượt còn lại</p>
    </div>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-dark rounded-pill px-4">
        <i class="fa-solid fa-plus me-1"></i>Tạo mã giảm giá
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="{{ route('admin.vouchers.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-7">
            <label for="search" class="form-label small fw-bold">Tìm mã</label>
            <input id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Nhập mã giảm giá">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label small fw-bold">Trạng thái</label>
            <select id="status" name="status" class="form-select">
                <option value="all" @selected($status === 'all')>Tất cả</option>
                <option value="active" @selected($status === 'active')>Đang bật</option>
                <option value="inactive" @selected($status === 'inactive')>Đang tắt</option>
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
                    <th class="ps-4">Mã giảm giá</th>
                    <th>Loại ưu đãi</th>
                    <th>Giá trị</th>
                    <th>Lượt còn lại</th>
                    <th>Đơn đã dùng</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $voucher->MaCode }}</td>
                        <td>{{ match($voucher->LoaiGiamGia) { 'percent' => 'Phần trăm', 'shipping' => 'Phí vận chuyển', default => 'Tiền mặt' } }}</td>
                        <td class="fw-semibold">
                            @if($voucher->LoaiGiamGia === 'percent')
                                {{ rtrim(rtrim(number_format((float) $voucher->GiaTri, 2, '.', ''), '0'), '.') }}%
                            @elseif($voucher->LoaiGiamGia === 'shipping')
                                {{ (float) $voucher->GiaTri <= 0 ? 'Miễn phí vận chuyển' : 'Tối đa ' . number_format((float) $voucher->GiaTri, 0, ',', '.') . '₫' }}
                            @else
                                {{ number_format((float) $voucher->GiaTri, 0, ',', '.') }}₫
                            @endif
                        </td>
                        <td>{{ number_format($voucher->SoLuong) }}</td>
                        <td>{{ number_format($voucher->don_hangs_count) }}</td>
                        <td>
                            @if(!$voucher->TrangThai)
                                <span class="badge bg-secondary-subtle text-secondary border">Đang tắt</span>
                            @elseif($voucher->SoLuong <= 0)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">Hết lượt</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success">Đang bật</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.vouchers.edit', $voucher->MaCode) }}" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-pen-to-square me-1"></i>Sửa</a>
                                <form action="{{ route('admin.vouchers.status', $voucher->MaCode) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn {{ $voucher->TrangThai ? 'tắt' : 'bật' }} mã này?');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm {{ $voucher->TrangThai ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit">{{ $voucher->TrangThai ? 'Tắt' : 'Bật' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Chưa có mã giảm giá.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">{{ $vouchers->links('pagination::bootstrap-5') }}</div>
@endsection

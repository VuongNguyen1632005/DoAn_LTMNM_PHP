@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Quản lý Đánh giá sản phẩm</h2>
        <p class="text-muted mb-0">Kiểm duyệt và quản lý các nhận xét từ khách hàng</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Mã ĐG</th>
                    <th>Sản phẩm</th>
                    <th>Người đánh giá</th>
                    <th>Số sao</th>
                    <th>Nội dung nhận xét</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $rev)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $rev->MaDanhGia }}</td>
                        <td>
                            <a href="{{ route('products.detail', $rev->MaSP) }}" target="_blank" class="text-decoration-none fw-bold text-dark">
                                {{ $rev->sanPham->TenSanPham ?? $rev->MaSP }}
                            </a>
                        </td>
                        <td>
                            <div>{{ $rev->TenKhachHang }}</div>
                            <small class="text-muted">{{ $rev->created_at ? $rev->created_at->format('d/m/Y') : '' }}</small>
                        </td>
                        <td class="text-warning">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fa-{{ $s <= $rev->SoSao ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </td>
                        <td style="max-width: 300px;">
                            <span class="small text-secondary">{{ $rev->NoiDung }}</span>
                        </td>
                        <td>
                            @if($rev->TrangThai)
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Hiển thị</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Đang ẩn</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <form action="{{ route('admin.reviews.visibility', $rev->MaDanhGia) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $rev->TrangThai ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $rev->TrangThai ? 'Ẩn đánh giá' : 'Hiển thị đánh giá' }}">
                                        <i class="fa-solid {{ $rev->TrangThai ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        {{ $rev->TrangThai ? 'Ẩn' : 'Hiện' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.reviews.destroy', $rev->MaDanhGia) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có đánh giá nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $reviews->links('pagination::bootstrap-5') }}
</div>
@endsection

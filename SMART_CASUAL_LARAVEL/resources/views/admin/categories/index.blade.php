@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Quản lý Danh mục</h2>
        <p class="text-muted mb-0">Danh sách phân loại sản phẩm thời trang</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark rounded-pill px-4">
        <i class="fa-solid fa-plus me-1"></i>Thêm danh mục mới
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Mã danh mục</th>
                    <th>Tên danh mục</th>
                    <th>Số lượng sản phẩm</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $cat->MaDanhMuc }}</td>
                        <td class="fw-bold">{{ $cat->TenDanhMuc }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $cat->san_phams_count }} sản phẩm</span>
                        </td>
                        <td>
                            @if($cat->TrangThai)
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary-subtle text-muted border px-2 py-1">Tạm khóa</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.categories.edit', $cat->MaDanhMuc) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                @if($cat->san_phams_count == 0)
                                    <form action="{{ route('admin.categories.destroy', $cat->MaDanhMuc) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có danh mục nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

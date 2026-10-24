@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Quản lý Sản phẩm</h2>
        <p class="text-muted mb-0">Quản lý danh mục, giá bán, tồn kho và trạng thái hiển thị của sản phẩm</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark rounded-pill px-4">
        <i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm mới
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold" for="search">Tìm kiếm</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control" id="search" name="search" value="{{ $search }}" placeholder="Nhập tên hoặc mã sản phẩm...">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold" for="category">Danh mục</label>
            <select class="form-select" id="category" name="category">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->MaDanhMuc }}" {{ $selectedCategory === $cat->MaDanhMuc ? 'selected' : '' }}>
                        {{ $cat->TenDanhMuc }} ({{ $cat->MaDanhMuc }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-bold" for="status">Trạng thái</label>
            <select class="form-select" id="status" name="status">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Đang bán</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Đang ẩn</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-dark rounded-pill">
                <i class="fa-solid fa-filter me-1"></i>Lọc
            </button>
        </div>
    </form>
</div>

<!-- Product Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Mã SP</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    @php
                        $img = $p->anhChinh ? '/' . ltrim($p->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $p->MaSP }}</td>
                        <td>
                            <img src="{{ $img }}" alt="{{ $p->TenSanPham }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->TenSanPham }}</div>
                            <small class="text-muted">{{ $p->chiTietSanPhams->count() }} biến thể size</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->danhMuc->TenDanhMuc ?? $p->MaDanhMuc }}</span>
                        </td>
                        <td class="fw-bold text-danger">
                            {{ number_format($p->gia_hien_thi, 0, ',', '.') }}₫
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark">{{ $p->tong_ton_kho }} chiếc</span>
                        </td>
                        <td>
                            @if($p->TrangThai)
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Đang bán</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Đang ẩn</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.products.edit', $p->MaSP) }}" class="btn btn-sm btn-outline-dark" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>

                                @if($p->TrangThai)
                                    <form action="{{ route('admin.products.destroy', $p->MaSP) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn ẩn sản phẩm này khỏi website?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Ẩn sản phẩm">
                                            <i class="fa-solid fa-eye-slash"></i> Ẩn
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.products.restore', $p->MaSP) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Khôi phục">
                                            <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open display-4 d-block mb-2"></i>
                            Không tìm thấy sản phẩm nào phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection

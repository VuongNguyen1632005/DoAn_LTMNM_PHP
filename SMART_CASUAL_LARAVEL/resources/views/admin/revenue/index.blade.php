@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Báo cáo & Thống kê Doanh thu</h2>
        <p class="text-muted mb-0">Thống kê doanh thu theo đơn hàng thành công, phương thức thanh toán và theo tháng</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card card-stat p-4 bg-white">
            <span class="text-muted small text-uppercase fw-bold">Tổng doanh thu thực tế</span>
            <h2 class="fw-bold text-success mt-2 mb-1">{{ number_format($totalRevenue, 0, ',', '.') }}₫</h2>
            <small class="text-muted">Từ {{ $deliveredCount }} đơn hàng giao thành công</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card card-stat p-4 bg-white">
            <span class="text-muted small text-uppercase fw-bold">Giá trị trung bình / đơn</span>
            @php $aov = $deliveredCount > 0 ? ($totalRevenue / $deliveredCount) : 0; @endphp
            <h2 class="fw-bold text-primary mt-2 mb-1">{{ number_format($aov, 0, ',', '.') }}₫</h2>
            <small class="text-muted">AOV (Average Order Value)</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Monthly Revenue Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 ps-4 border-bottom">
                <h5 class="fw-bold mb-0">Doanh thu theo các tháng (Năm {{ date('Y') }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tháng</th>
                            <th>Số đơn thành công</th>
                            <th class="text-end pe-4">Doanh thu tháng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyRevenue as $row)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">Tháng {{ $row->month }}/{{ date('Y') }}</td>
                                <td>{{ $row->orders_count }} đơn</td>
                                <td class="text-end pe-4 fw-bold text-success">{{ number_format($row->revenue, 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">Chưa có dữ liệu doanh thu tháng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Cơ cấu đơn hàng theo trạng thái</h5>
            <div class="d-flex flex-column gap-3">
                @foreach($statusBreakdown as $sb)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $badge = match($sb->TrangThaiDonHang) {
                                    'pending' => 'badge-pending',
                                    'confirmed' => 'badge-confirmed',
                                    'packing' => 'badge-packing',
                                    'shipping' => 'badge-shipping',
                                    'delivered' => 'badge-delivered',
                                    'cancelled' => 'badge-cancelled',
                                    default => 'bg-light text-dark'
                                };
                                $label = match($sb->TrangThaiDonHang) {
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'packing' => 'Đang đóng gói',
                                    'shipping' => 'Đang giao hàng',
                                    'delivered' => 'Đã giao thành công',
                                    'cancelled' => 'Đã hủy',
                                    default => $sb->TrangThaiDonHang
                                };
                            @endphp
                            <span class="badge {{ $badge }} rounded-pill px-3 py-1 me-2">{{ $label }}</span>
                            <span class="text-muted small">({{ $sb->count }} đơn)</span>
                        </div>
                        <strong class="text-dark">{{ number_format($sb->total, 0, ',', '.') }}₫</strong>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Doanh thu theo phương thức thanh toán</h5>
            <div class="d-flex flex-column gap-2">
                @foreach($paymentBreakdown as $pb)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $pb->PhuongThucThanhToan }} ({{ $pb->count }} đơn)</span>
                        <span class="text-success fw-bold">{{ number_format($pb->total, 0, ',', '.') }}₫</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

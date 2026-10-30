@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <h2 class="h3 fw-bold mb-1">Báo cáo & Thống kê Doanh thu</h2>
        <p class="text-muted mb-0">Thống kê doanh thu theo đơn hàng giao thành công và kỳ báo cáo</p>
    </div>
    <form method="GET" action="{{ route('admin.revenue.index') }}" class="d-flex align-items-center gap-2">
        <label for="revenue-year" class="form-label mb-0 fw-semibold">Năm báo cáo</label>
        <select id="revenue-year" name="year" class="form-select" onchange="this.form.submit()" aria-label="Chọn năm báo cáo">
            @foreach($availableYears as $yearOption)
                <option value="{{ $yearOption }}" @selected($yearOption === $selectedYear)>{{ $yearOption }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card card-stat p-4 bg-white">
            <span class="text-muted small text-uppercase fw-bold">Tổng doanh thu thực tế năm {{ $selectedYear }}</span>
            <h2 class="fw-bold text-success mt-2 mb-1">{{ number_format($totalRevenue, 0, ',', '.') }}₫</h2>
            <small class="text-muted">Từ {{ $deliveredCount }} đơn hàng giao thành công</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card card-stat p-4 bg-white">
            <span class="text-muted small text-uppercase fw-bold">Giá trị trung bình / đơn năm {{ $selectedYear }}</span>
            @php $aov = $deliveredCount > 0 ? ($totalRevenue / $deliveredCount) : 0; @endphp
            <h2 class="fw-bold text-primary mt-2 mb-1">{{ number_format($aov, 0, ',', '.') }}₫</h2>
            <small class="text-muted">AOV (Average Order Value)</small>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <h5 class="fw-bold mb-1">Biểu đồ doanh thu theo tháng</h5>
    <p class="small text-muted mb-4">Năm {{ $selectedYear }} · Chỉ tính đơn hàng giao thành công</p>
    @php $maxMonthlyRevenue = max(1, $monthlyChart->max('revenue')); @endphp
    <div class="revenue-chart" role="img" aria-label="Biểu đồ cột doanh thu theo 12 tháng năm {{ $selectedYear }}">
        @foreach($monthlyChart as $point)
            @php $barHeight = $point['revenue'] > 0 ? max(5, ($point['revenue'] / $maxMonthlyRevenue) * 100) : 0; @endphp
            <div class="revenue-chart__item" title="Tháng {{ $point['month'] }}: {{ number_format($point['revenue'], 0, ',', '.') }}₫ · {{ $point['orders'] }} đơn">
                <span class="revenue-chart__value">{{ $point['revenue'] > 0 ? number_format($point['revenue'] / 1000000, 1, ',', '.') . 'tr' : '' }}</span>
                <div class="revenue-chart__bar-wrap">
                    <div class="revenue-chart__bar {{ $point['revenue'] > 0 ? '' : 'revenue-chart__bar--empty' }}" style="height: {{ $barHeight }}%"></div>
                </div>
                <span class="revenue-chart__label">T{{ $point['month'] }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="row g-4">
    <!-- Monthly Revenue Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 ps-4 border-bottom">
                <h5 class="fw-bold mb-0">Doanh thu theo các tháng (Năm {{ $selectedYear }})</h5>
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
                                <td class="ps-4 fw-bold text-dark">Tháng {{ $row->month }}/{{ $selectedYear }}</td>
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

@push('scripts')
<style>
    .revenue-chart { display: flex; align-items: stretch; gap: 10px; min-height: 210px; padding: 0 8px; }
    .revenue-chart__item { display: flex; flex: 1 1 0; min-width: 0; flex-direction: column; align-items: center; }
    .revenue-chart__value { height: 22px; color: #556273; font-size: .72rem; white-space: nowrap; }
    .revenue-chart__bar-wrap { display: flex; width: 100%; flex: 1; align-items: flex-end; justify-content: center; border-bottom: 1px solid #dee2e6; }
    .revenue-chart__bar { width: min(54px, 72%); min-height: 3px; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #42b983, #168a5b); transition: filter .15s ease; }
    .revenue-chart__item:hover .revenue-chart__bar { filter: brightness(.9); }
    .revenue-chart__bar--empty { background: #e9eef2; }
    .revenue-chart__label { padding-top: 8px; color: #667085; font-size: .78rem; }
    @media (max-width: 576px) { .revenue-chart { gap: 4px; padding: 0; } .revenue-chart__value { font-size: .58rem; } }
</style>
@endpush

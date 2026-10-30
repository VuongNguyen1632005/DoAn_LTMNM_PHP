<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DonHang;

class AdminRevenueController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = DonHang::where('TrangThaiDonHang', 'delivered')
            ->selectRaw('YEAR(NgayDat) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year);

        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int) now()->format('Y')]);
        }

        $requestedYear = filter_var($request->query('year'), FILTER_VALIDATE_INT);
        $year = $availableYears->contains($requestedYear) ? $requestedYear : $availableYears->first();

        // Revenue cards, chart, and table all use the selected reporting year.
        $yearOrders = DonHang::where('TrangThaiDonHang', 'delivered')->whereYear('NgayDat', $year);
        $totalDeliveredRevenue = (float) (clone $yearOrders)->sum('ThanhTien');
        $totalDeliveredCount = (clone $yearOrders)->count();

        // Total orders count by status
        $statusBreakdown = DonHang::select('TrangThaiDonHang', DB::raw('count(*) as count'), DB::raw('sum(ThanhTien) as total'))
            ->groupBy('TrangThaiDonHang')
            ->get();

        // Monthly revenue for the selected year
        $monthlyRevenue = DonHang::select(
                DB::raw('MONTH(NgayDat) as month'),
                DB::raw('sum(ThanhTien) as revenue'),
                DB::raw('count(*) as orders_count')
            )
            ->where('TrangThaiDonHang', 'delivered')
            ->whereYear('NgayDat', $year)
            ->groupBy(DB::raw('MONTH(NgayDat)'))
            ->orderBy('month')
            ->get();

        $monthlyChart = collect(range(1, 12))->map(function ($month) use ($monthlyRevenue) {
            $row = $monthlyRevenue->firstWhere('month', $month);

            return [
                'month' => $month,
                'revenue' => (float) ($row->revenue ?? 0),
                'orders' => (int) ($row->orders_count ?? 0),
            ];
        });

        // Payment method breakdown
        $paymentBreakdown = DonHang::select('PhuongThucThanhToan', DB::raw('count(*) as count'), DB::raw('sum(ThanhTien) as total'))
            ->where('TrangThaiDonHang', 'delivered')
            ->groupBy('PhuongThucThanhToan')
            ->get();

        return view('admin.revenue.index', [
            'title' => 'Báo cáo thống kê Doanh thu',
            'totalRevenue' => $totalDeliveredRevenue,
            'deliveredCount' => $totalDeliveredCount,
            'statusBreakdown' => $statusBreakdown,
            'monthlyRevenue' => $monthlyRevenue,
            'paymentBreakdown' => $paymentBreakdown,
            'monthlyChart' => $monthlyChart,
            'availableYears' => $availableYears,
            'selectedYear' => $year,
        ]);
    }
}

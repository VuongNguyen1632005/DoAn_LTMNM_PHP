<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\DonHang;

class AdminRevenueController extends Controller
{
    public function index()
    {
        // Total delivered revenue
        $totalDeliveredRevenue = (float) DonHang::where('TrangThaiDonHang', 'delivered')->sum('ThanhTien');
        $totalDeliveredCount = DonHang::where('TrangThaiDonHang', 'delivered')->count();

        // Total orders count by status
        $statusBreakdown = DonHang::select('TrangThaiDonHang', DB::raw('count(*) as count'), DB::raw('sum(ThanhTien) as total'))
            ->groupBy('TrangThaiDonHang')
            ->get();

        // Monthly revenue for the current year
        $monthlyRevenue = DonHang::select(
                DB::raw('MONTH(NgayDat) as month'),
                DB::raw('sum(ThanhTien) as revenue'),
                DB::raw('count(*) as orders_count')
            )
            ->where('TrangThaiDonHang', 'delivered')
            ->whereYear('NgayDat', date('Y'))
            ->groupBy(DB::raw('MONTH(NgayDat)'))
            ->orderBy('month')
            ->get();

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
        ]);
    }
}

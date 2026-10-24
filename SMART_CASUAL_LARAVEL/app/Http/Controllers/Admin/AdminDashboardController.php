<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\DanhMuc;
use App\Models\DanhGia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = SanPham::count();
        $totalCategories = DanhMuc::count();
        $totalCustomers = KhachHang::count();
        $totalOrders = DonHang::count();

        $pendingOrders = DonHang::where('TrangThaiDonHang', 'pending')->count();
        $deliveredOrders = DonHang::where('TrangThaiDonHang', 'delivered')->count();

        // Revenue from delivered or paid orders
        $totalRevenue = (float) DonHang::where('TrangThaiDonHang', 'delivered')
            ->orWhere('DaThanhToan', true)
            ->sum('ThanhTien');

        $recentOrders = DonHang::with(['khachHang', 'phuongThucVanChuyen'])
            ->latest('NgayDat')
            ->take(5)
            ->get();

        $recentReviews = DanhGia::with(['sanPham'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', [
            'title' => 'Bảng điều khiển quản trị',
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalCustomers' => $totalCustomers,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'deliveredOrders' => $deliveredOrders,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
            'recentReviews' => $recentReviews,
        ]);
    }
}

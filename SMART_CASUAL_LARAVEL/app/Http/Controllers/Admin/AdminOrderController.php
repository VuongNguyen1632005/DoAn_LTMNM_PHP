<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DonHang;
use App\Services\CheckoutService;
use Exception;

class AdminOrderController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = trim($request->input('search', ''));

        $query = DonHang::with(['khachHang', 'phuongThucVanChuyen']);

        if ($status !== 'all' && $status !== '') {
            $query->where('TrangThaiDonHang', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('MaDonHang', 'LIKE', '%' . $search . '%')
                  ->orWhere('TenNguoiNhan', 'LIKE', '%' . $search . '%')
                  ->orWhere('SoDienThoai', 'LIKE', '%' . $search . '%');
            });
        }

        $orders = $query->latest('NgayDat')->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'title' => 'Quản lý Đơn hàng',
            'orders' => $orders,
            'selectedStatus' => $status,
            'search' => $search,
        ]);
    }

    public function show(string $id)
    {
        $order = DonHang::with([
            'chiTietDonHangs.chiTietSanPham.sanPham.anhChinh',
            'chiTietDonHangs.chiTietSanPham.kichThuoc',
            'khachHang',
            'nhanVien',
            'phuongThucVanChuyen',
            'maGiamGia',
        ])->where('MaDonHang', $id)->firstOrFail();

        return view('admin.orders.show', [
            'title' => "Quản lý đơn hàng {$order->MaDonHang}",
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $order = DonHang::where('MaDonHang', $id)->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,packing,shipping,delivered,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        $newStatus = $validated['status'];
        $staff = Auth::guard('admin')->user();

        try {
            $updatedOrder = $this->checkoutService->updateOrderStatus(
                $order,
                $newStatus,
                $staff ? $staff->MaNV : null,
                $validated['note'] ?? null
            );

            return redirect()->back()->with('success', "Đã cập nhật trạng thái đơn hàng thành '{$updatedOrder->trang_thai_tieng_viet}'.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function togglePayment(Request $request, string $id)
    {
        $order = DonHang::where('MaDonHang', $id)->firstOrFail();
        $isPaid = (bool) $request->input('is_paid', true);

        $order->update(['DaThanhToan' => $isPaid]);

        $msg = $isPaid ? 'Đã xác nhận thanh toán cho đơn hàng.' : 'Đã đánh dấu đơn hàng chưa thanh toán.';
        return redirect()->back()->with('success', $msg);
    }
}

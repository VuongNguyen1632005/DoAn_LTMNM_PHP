<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DonHang;
use App\Services\CheckoutService;
use Exception;

class OrderController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    public function index()
    {
        $customer = Auth::guard('web')->user();
        if (!$customer) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem lịch sử đơn hàng.');
        }

        $orders = DonHang::with(['chiTietDonHangs.chiTietSanPham.sanPham.anhChinh', 'phuongThucVanChuyen'])
            ->where('MaKH', $customer->MaKH)
            ->latest('NgayDat')
            ->paginate(10);

        return view('orders.index', [
            'title' => 'Lịch sử đơn hàng của tôi',
            'orders' => $orders,
        ]);
    }

    public function detail(string $id)
    {
        $customer = Auth::guard('web')->user();
        if (!$customer) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem chi tiết đơn hàng.');
        }

        $order = DonHang::with([
            'chiTietDonHangs.chiTietSanPham.sanPham.anhChinh',
            'chiTietDonHangs.chiTietSanPham.kichThuoc',
            'phuongThucVanChuyen',
            'maGiamGia',
            'khachHang'
        ])->where('MaDonHang', $id)->firstOrFail();

        // Strict ownership verification: Prevent IDOR
        if (!$order->MaKH || $order->MaKH !== $customer->MaKH) {
            abort(403, 'Bạn không có quyền xem thông tin đơn hàng này.');
        }

        return view('orders.detail', [
            'title' => "Chi tiết đơn hàng {$order->MaDonHang}",
            'order' => $order,
        ]);
    }

    public function confirmReceived(Request $request, string $id)
    {
        $customer = Auth::guard('web')->user();
        if (!$customer) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện thao tác.');
        }

        $order = DonHang::where('MaDonHang', $id)->firstOrFail();

        // Strict ownership verification
        if (!$order->MaKH || $order->MaKH !== $customer->MaKH) {
            abort(403, 'Bạn không có quyền thao tác trên đơn hàng này.');
        }

        if ($order->TrangThaiDonHang === 'delivered') {
            return redirect()->back()->with('info', 'Đơn hàng này đã được xác nhận đã nhận.');
        }

        try {
            $this->checkoutService->updateOrderStatus($order, 'delivered', null, 'Khách hàng xác nhận đã nhận hàng');
            return redirect()->back()->with('success', 'Xác nhận đã nhận hàng thành công! Đơn hàng đã hoàn tất.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, string $id)
    {
        $customer = Auth::guard('web')->user();
        if (!$customer) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện thao tác.');
        }

        $order = DonHang::where('MaDonHang', $id)->firstOrFail();

        // Strict ownership verification
        if (!$order->MaKH || $order->MaKH !== $customer->MaKH) {
            abort(403, 'Bạn không có quyền thao tác trên đơn hàng này.');
        }

        try {
            $this->checkoutService->updateOrderStatus($order, 'cancelled', null, 'Khách hàng tự hủy');
            return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công và hoàn trả số lượng vào kho.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

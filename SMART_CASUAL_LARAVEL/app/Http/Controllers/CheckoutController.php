<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use App\Services\CartService;
use App\Models\PhuongThucVanChuyen;
use App\Models\MaGiamGia;
use Exception;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected CartService $cartService
    ) {
    }

    public function index(Request $request)
    {
        $items = $this->cartService->getItems();
        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $shippingCode = $request->query('shipping_code', 'HT');
        $voucherCode = $request->query('voucher_code');

        $preview = $this->checkoutService->preview($shippingCode, $voucherCode);
        $shippingMethods = PhuongThucVanChuyen::where('TrangThai', true)->get();

        $user = Auth::guard('web')->user();

        return view('checkout.index', [
            'title' => 'Thanh toán đơn hàng',
            'items' => $preview['items'],
            'subtotal' => $preview['subtotal'],
            'shipping_fee' => $preview['shipping_fee'],
            'discount_amount' => $preview['discount_amount'],
            'grand_total' => $preview['grand_total'],
            'voucher' => $preview['voucher'],
            'voucher_error' => $preview['voucher_error'],
            'voucher_code' => $voucherCode,
            'selected_shipping' => $shippingCode,
            'shipping_methods' => $shippingMethods,
            'customer' => $user,
        ]);
    }

    public function process(CheckoutRequest $request)
    {
        $validated = $request->validated();

        $data = [
            'receiver_name' => $validated['receiver_name'],
            'receiver_phone' => $validated['receiver_phone'],
            'receiver_email' => $validated['receiver_email'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'shipping_code' => $validated['shipping_code'],
            'payment_method' => $validated['payment_method'],
            'voucher_code' => $validated['voucher_code'] ?? null,
            'note' => $validated['note'] ?? null,
            'customer_id' => Auth::guard('web')->check() ? Auth::guard('web')->user()->MaKH : null,
            'idempotency_key' => $validated['idempotency_key'] ?? null,
        ];

        try {
            $order = $this->checkoutService->placeOrder($data);

            if (Auth::guard('web')->check()) {
                return redirect()->route('orders.detail', $order->MaDonHang)
                    ->with('success', "Đặt hàng thành công! Mã đơn hàng của bạn là {$order->MaDonHang}.");
            }

            return redirect()->route('home')
                ->with('success', "Đặt hàng thành công! Mã đơn hàng của bạn là {$order->MaDonHang}. Chúng tôi sẽ liên hệ sớm nhất để giao hàng.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}

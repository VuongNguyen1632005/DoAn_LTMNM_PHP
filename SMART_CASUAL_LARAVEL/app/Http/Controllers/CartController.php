<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\ChiTietSanPham;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index()
    {
        $items = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();
        $totalQuantity = $this->cartService->getTotalQuantity();

        return view('cart.index', [
            'title' => 'Giỏ hàng của bạn',
            'items' => $items,
            'subtotal' => $subtotal,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|string|exists:CHI_TIET_SP,MaCTSP',
            'quantity' => 'nullable|integer|min:1|max:100',
        ], [
            'variant_id.required' => 'Vui lòng chọn size sản phẩm.',
            'variant_id.exists' => 'Size sản phẩm không tồn tại.',
            'quantity.min' => 'Số lượng mua tối thiểu là 1.',
        ]);

        $variantId = $request->input('variant_id');
        $qty = (int) $request->input('quantity', 1);

        $success = $this->cartService->addItem($variantId, $qty);
        if (!$success) {
            return redirect()->back()->with('error', 'Sản phẩm này tạm thời đã hết hàng hoặc không đủ số lượng.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => $this->cartService->getTotalQuantity(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|string',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $variantId = $request->input('variant_id');
        $qty = (int) $request->input('quantity');

        $updated = $this->cartService->updateQuantity($variantId, $qty);
        if (!$updated) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sản phẩm này hiện đã hết hàng hoặc không đủ số lượng trong kho.',
                ], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Sản phẩm này hiện đã hết hàng hoặc không đủ số lượng trong kho.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã cập nhật số lượng giỏ hàng.',
                'subtotal' => $this->cartService->getSubtotal(),
                'cart_count' => $this->cartService->getTotalQuantity(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|string',
        ]);

        $this->cartService->removeItem($request->input('variant_id'));

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clear()
    {
        $this->cartService->clear();
        return redirect()->route('cart.index')->with('success', 'Đã làm trống giỏ hàng.');
    }
}

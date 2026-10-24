<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\ChiTietSanPham;
use App\Models\GioHang;

class CartService
{
    private const SESSION_KEY = 'smart_casual_guest_cart';

    /**
     * Get all cart items with product and variant details.
     * @return array<int, array{variant_id: string, product_id: string, product_name: string, size: string, price: float, quantity: int, stock: int, image_url: string, line_total: float}>
     */
    public function getItems(): array
    {
        if (Auth::guard('web')->check()) {
            $customer = Auth::guard('web')->user();
            $cartRows = GioHang::with(['chiTietSanPham.sanPham.anhChinh', 'chiTietSanPham.kichThuoc'])
                ->where('MaKH', $customer->MaKH)
                ->get();

            $items = [];
            foreach ($cartRows as $row) {
                $variant = $row->chiTietSanPham;
                if (!$variant || !$variant->sanPham) continue;

                $sp = $variant->sanPham;
                $img = $sp->anhChinh ? '/' . ltrim($sp->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
                $price = (float) $variant->GiaBan;
                $qty = (int) $row->SoLuong;

                $items[] = [
                    'variant_id' => $variant->MaCTSP,
                    'product_id' => $sp->MaSP,
                    'product_name' => $sp->TenSanPham,
                    'size' => $variant->kichThuoc ? $variant->kichThuoc->TenKichThuoc : $variant->MaKichThuoc,
                    'price' => $price,
                    'quantity' => $qty,
                    'stock' => (int) $variant->SoLuong,
                    'image_url' => $img,
                    'line_total' => $price * $qty,
                ];
            }
            return $items;
        }

        // Guest Session
        $raw = Session::get(self::SESSION_KEY, []);
        $items = [];
        foreach ($raw as $variantId => $qty) {
            $variant = ChiTietSanPham::with(['sanPham.anhChinh', 'kichThuoc'])->find($variantId);
            if (!$variant || !$variant->sanPham) continue;

            $sp = $variant->sanPham;
            $img = $sp->anhChinh ? '/' . ltrim($sp->anhChinh->DuongDan, '/') : '/assets/img/product-details/product-details-1-1/product-1-1.png';
            $price = (float) $variant->GiaBan;
            $qty = (int) $qty;

            $items[] = [
                'variant_id' => $variant->MaCTSP,
                'product_id' => $sp->MaSP,
                'product_name' => $sp->TenSanPham,
                'size' => $variant->kichThuoc ? $variant->kichThuoc->TenKichThuoc : $variant->MaKichThuoc,
                'price' => $price,
                'quantity' => $qty,
                'stock' => (int) $variant->SoLuong,
                'image_url' => $img,
                'line_total' => $price * $qty,
            ];
        }
        return $items;
    }

    public function addItem(string $variantId, int $quantity = 1): bool
    {
        $variant = ChiTietSanPham::find($variantId);
        if (!$variant || !$variant->TrangThai || $variant->SoLuong < 1) {
            return false;
        }

        $addQty = max(1, $quantity);

        if (Auth::guard('web')->check()) {
            $customer = Auth::guard('web')->user();
            $existing = GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variantId)->first();
            $newQty = $existing ? ($existing->SoLuong + $addQty) : $addQty;
            $finalQty = min($newQty, (int) $variant->SoLuong);

            if ($existing) {
                GioHang::where('MaKH', $customer->MaKH)
                    ->where('MaCTSP', $variantId)
                    ->update(['SoLuong' => $finalQty]);
            } else {
                GioHang::create([
                    'MaKH' => $customer->MaKH,
                    'MaCTSP' => $variantId,
                    'SoLuong' => $finalQty,
                ]);
            }
            return true;
        }

        // Guest
        $raw = Session::get(self::SESSION_KEY, []);
        $current = $raw[$variantId] ?? 0;
        $finalQty = min($current + $addQty, (int) $variant->SoLuong);
        $raw[$variantId] = $finalQty;
        Session::put(self::SESSION_KEY, $raw);
        return true;
    }

    public function updateQuantity(string $variantId, int $quantity): bool
    {
        $variant = ChiTietSanPham::find($variantId);
        if (!$variant || !$variant->TrangThai || (int) $variant->SoLuong < 1) {
            return false;
        }

        if ($quantity < 1) {
            return false;
        }

        $targetQty = min($quantity, (int) $variant->SoLuong);

        if (Auth::guard('web')->check()) {
            $customer = Auth::guard('web')->user();
            GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variantId)
                ->update(['SoLuong' => $targetQty]);
            return true;
        }

        $raw = Session::get(self::SESSION_KEY, []);
        if (isset($raw[$variantId])) {
            $raw[$variantId] = $targetQty;
            Session::put(self::SESSION_KEY, $raw);
        }
        return true;
    }

    public function removeItem(string $variantId): void
    {
        if (Auth::guard('web')->check()) {
            $customer = Auth::guard('web')->user();
            GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variantId)->delete();
            return;
        }

        $raw = Session::get(self::SESSION_KEY, []);
        unset($raw[$variantId]);
        Session::put(self::SESSION_KEY, $raw);
    }

    public function clear(): void
    {
        if (Auth::guard('web')->check()) {
            $customer = Auth::guard('web')->user();
            GioHang::where('MaKH', $customer->MaKH)->delete();
        }
        Session::forget(self::SESSION_KEY);
    }

    public function getSubtotal(): float
    {
        $items = $this->getItems();
        return (float) array_sum(array_column($items, 'line_total'));
    }

    public function getTotalQuantity(): int
    {
        $items = $this->getItems();
        return (int) array_sum(array_column($items, 'quantity'));
    }

    /**
     * Merge guest session cart into database cart when customer logs in
     */
    public function mergeGuestCartToCustomer(string $customerId): void
    {
        $raw = Session::get(self::SESSION_KEY, []);
        if (empty($raw)) return;

        foreach ($raw as $variantId => $qty) {
            $variant = ChiTietSanPham::find($variantId);
            if (!$variant || !$variant->TrangThai || (int) $variant->SoLuong < 1) continue;

            $existing = GioHang::where('MaKH', $customerId)->where('MaCTSP', $variantId)->first();
            $newQty = $existing ? ($existing->SoLuong + (int)$qty) : (int)$qty;
            $finalQty = max(1, min($newQty, (int) $variant->SoLuong));

            if ($existing) {
                GioHang::where('MaKH', $customerId)
                    ->where('MaCTSP', $variantId)
                    ->update(['SoLuong' => $finalQty]);
            } else {
                GioHang::create([
                    'MaKH' => $customerId,
                    'MaCTSP' => $variantId,
                    'SoLuong' => $finalQty,
                ]);
            }
        }

        Session::forget(self::SESSION_KEY);
    }
}

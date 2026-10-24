<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietSanPham;
use App\Models\MaGiamGia;
use App\Models\PhuongThucVanChuyen;
use App\Models\CheckoutIdempotency;
use Illuminate\Database\QueryException;
use Exception;

class CheckoutService
{
    public const ALLOWED_TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['packing', 'cancelled'],
        'packing' => ['shipping', 'cancelled'],
        'shipping' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];
    public function __construct(protected CartService $cartService)
    {
    }

    /**
     * Preview checkout summary with given shipping method and voucher code.
     * @return array{items: array, subtotal: float, shipping_fee: float, discount_amount: float, grand_total: float, voucher: ?MaGiamGia, shipping_method: ?PhuongThucVanChuyen}
     */
    public function preview(?string $shippingCode = 'HT', ?string $voucherCode = null): array
    {
        $items = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();

        $shippingMethod = $shippingCode ? PhuongThucVanChuyen::where('MaPTVC', $shippingCode)->where('TrangThai', true)->first() : null;
        $shippingFee = $shippingMethod ? (float) $shippingMethod->GiaVanChuyen : 0.0;

        $voucher = null;
        $discountAmount = 0.0;
        if ($voucherCode) {
            $voucher = MaGiamGia::where('MaCode', strtoupper(trim($voucherCode)))->where('TrangThai', true)->first();
            if ($voucher && $voucher->isValid()) {
                $discountAmount = $voucher->calculateDiscount($subtotal, $shippingFee);
            }
        }

        $grandTotal = max(0.0, $subtotal + $shippingFee - $discountAmount);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
            'voucher' => $voucher,
            'shipping_method' => $shippingMethod,
        ];
    }

    /**
     * Process order atomically inside a DB transaction with idempotency protection.
     * @param array{receiver_name: string, receiver_phone: string, receiver_email?: ?string, shipping_address: string, note?: ?string, payment_method?: string, shipping_code?: string, voucher_code?: ?string, customer_id?: ?string, idempotency_key?: ?string} $data
     * @throws Exception
     */
    public function placeOrder(array $data, ?string $idempotencyKey = null): DonHang
    {
        $idempotencyKey = $idempotencyKey ?? ($data['idempotency_key'] ?? null);

        if ($idempotencyKey) {
            $payloadForHash = [
                'customer_id' => $data['customer_id'] ?? null,
                'receiver_name' => $data['receiver_name'] ?? null,
                'receiver_phone' => $data['receiver_phone'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'shipping_code' => $data['shipping_code'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'voucher_code' => $data['voucher_code'] ?? null,
            ];
            $requestHash = hash('sha256', json_encode($payloadForHash));

            $existingClaim = CheckoutIdempotency::where('idempotency_key', $idempotencyKey)->first();
            if ($existingClaim) {
                if ($existingClaim->request_hash !== $requestHash) {
                    throw new Exception('Idempotency key đã được sử dụng với thông tin đặt hàng khác.');
                }
                if ($existingClaim->status === 'completed' && $existingClaim->order_id) {
                    $existingOrder = DonHang::find($existingClaim->order_id);
                    if ($existingOrder) {
                        return $existingOrder;
                    }
                }
                if ($existingClaim->status === 'processing') {
                    throw new Exception('Yêu cầu thanh toán của bạn đang được xử lý, vui lòng không gửi lại request.');
                }
                // If previously failed, allow retry
                $existingClaim->update(['status' => 'processing']);
            } else {
                try {
                    CheckoutIdempotency::create([
                        'idempotency_key' => $idempotencyKey,
                        'customer_id' => $data['customer_id'] ?? null,
                        'request_hash' => $requestHash,
                        'status' => 'processing',
                    ]);
                } catch (QueryException $e) {
                    throw new Exception('Yêu cầu thanh toán đồng thời đã được gửi. Vui lòng không gửi lại request.');
                }
            }
        }

        try {
            $order = DB::transaction(function () use ($data) {
                $items = $this->cartService->getItems();
                if (empty($items)) {
                    throw new Exception('Giỏ hàng đang trống, không thể tiến hành đặt hàng.');
                }

                $subtotal = 0.0;
                $totalQty = 0;
                $lockedVariants = [];

                // 1. Check and lock stock for each variant
                foreach ($items as $item) {
                    /** @var ChiTietSanPham|null $variant */
                    $variant = ChiTietSanPham::where('MaCTSP', $item['variant_id'])->lockForUpdate()->first();
                    if (!$variant || !$variant->TrangThai) {
                        throw new Exception("Sản phẩm {$item['product_name']} ({$item['size']}) hiện không khả dụng.");
                    }

                    if ($variant->SoLuong < $item['quantity']) {
                        throw new Exception("Sản phẩm {$item['product_name']} ({$item['size']}) chỉ còn {$variant->SoLuong} sản phẩm trong kho.");
                    }

                    $lockedVariants[] = [
                        'variant' => $variant,
                        'quantity' => $item['quantity'],
                        'price' => (float) $variant->GiaBan,
                    ];

                    $subtotal += ((float) $variant->GiaBan) * $item['quantity'];
                    $totalQty += $item['quantity'];
                }

                // 2. Shipping method
                $shippingCode = $data['shipping_code'] ?? 'HT';
                $shipping = PhuongThucVanChuyen::where('MaPTVC', $shippingCode)->where('TrangThai', true)->first();
                $shippingFee = $shipping ? (float) $shipping->GiaVanChuyen : 0.0;

                // 3. Voucher
                $voucherCode = !empty($data['voucher_code']) ? strtoupper(trim($data['voucher_code'])) : null;
                $discountAmount = 0.0;
                if ($voucherCode) {
                    /** @var MaGiamGia|null $voucher */
                    $voucher = MaGiamGia::where('MaCode', $voucherCode)->lockForUpdate()->first();
                    if (!$voucher || !$voucher->isValid()) {
                        throw new Exception("Mã giảm giá '{$voucherCode}' không hợp lệ hoặc đã hết lượt sử dụng.");
                    }
                    $discountAmount = $voucher->calculateDiscount($subtotal, $shippingFee);
                    $voucher->decrement('SoLuong', 1);
                }

                $grandTotal = max(0.0, $subtotal + $shippingFee - $discountAmount);

                // 4. Generate unique Order ID
                $datePart = date('Ymd');
                $randomPart = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
                $orderId = 'DH' . $datePart . $randomPart;

                // 5. Create Order
                $order = DonHang::create([
                    'MaDonHang' => $orderId,
                    'MaKH' => $data['customer_id'] ?? null,
                    'MaNV' => null,
                    'TenNguoiNhan' => $data['receiver_name'],
                    'SoDienThoai' => $data['receiver_phone'],
                    'Email' => $data['receiver_email'] ?? null,
                    'MaCode' => $voucherCode,
                    'MaPTVC' => $shippingCode,
                    'NgayDat' => now(),
                    'DiaChiGiao' => $data['shipping_address'],
                    'GhiChu' => $data['note'] ?? null,
                    'TongSoLuong' => $totalQty,
                    'TongTien' => $subtotal,
                    'GiamGia' => $discountAmount,
                    'PhiVanChuyen' => $shippingFee,
                    'ThanhTien' => $grandTotal,
                    'PhuongThucThanhToan' => strtoupper($data['payment_method'] ?? 'COD'),
                    'DaThanhToan' => false,
                    'TrangThaiDonHang' => 'pending',
                    'TrangThai' => true,
                ]);

                // 6. Deduct stock & create Order Items
                foreach ($lockedVariants as $entry) {
                    /** @var ChiTietSanPham $v */
                    $v = $entry['variant'];
                    $qty = $entry['quantity'];
                    $price = $entry['price'];

                    $v->decrement('SoLuong', $qty);

                    ChiTietDonHang::create([
                        'MaDonHang' => $orderId,
                        'MaCTSP' => $v->MaCTSP,
                        'SoLuong' => $qty,
                        'DonGia' => $price,
                        'ThanhTien' => $price * $qty,
                        'TrangThai' => true,
                    ]);
                }

                // 7. Clear cart
                $this->cartService->clear();

                return $order;
            });

            if ($idempotencyKey) {
                CheckoutIdempotency::where('idempotency_key', $idempotencyKey)->update([
                    'status' => 'completed',
                    'order_id' => $order->MaDonHang,
                ]);
            }

            return $order;
        } catch (Exception $e) {
            if ($idempotencyKey) {
                CheckoutIdempotency::where('idempotency_key', $idempotencyKey)->update([
                    'status' => 'failed',
                ]);
            }
            throw $e;
        }
    }

    /**
     * Update order status with strict state transition validation and row locking.
     * @throws Exception
     */
    public function updateOrderStatus(DonHang $order, string $newStatus, ?string $staffId = null, ?string $note = null): DonHang
    {
        return DB::transaction(function () use ($order, $newStatus, $staffId, $note) {
            /** @var DonHang|null $lockedOrder */
            $lockedOrder = DonHang::where('MaDonHang', $order->MaDonHang)->lockForUpdate()->first();
            if (!$lockedOrder) {
                throw new Exception("Không tìm thấy đơn hàng {$order->MaDonHang}.");
            }

            $currentStatus = $lockedOrder->TrangThaiDonHang;

            if ($newStatus === 'cancelled') {
                if ($currentStatus === 'cancelled') {
                    throw new Exception("Đơn hàng này đã ở trạng thái 'Đã hủy'.");
                }
                $this->cancelOrder($lockedOrder, $note ?? 'Hủy đơn hàng');
                return $lockedOrder->fresh();
            }

            if ($currentStatus === $newStatus) {
                return $lockedOrder;
            }

            $allowed = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
            if (!in_array($newStatus, $allowed, true)) {
                $statusLabels = [
                    'pending' => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'packing' => 'Đang đóng gói',
                    'shipping' => 'Đang giao hàng',
                    'delivered' => 'Đã giao thành công',
                    'cancelled' => 'Đã hủy',
                ];
                $currentLabel = $statusLabels[$currentStatus] ?? $currentStatus;
                $newLabel = $statusLabels[$newStatus] ?? $newStatus;
                throw new Exception("Không thể chuyển trạng thái đơn hàng từ '{$currentLabel}' sang '{$newLabel}'.");
            }

            if ($newStatus === 'cancelled') {
                $this->cancelOrder($lockedOrder, $note ?? 'Hủy đơn hàng');
                return $lockedOrder->fresh();
            }

            $updateData = [
                'TrangThaiDonHang' => $newStatus,
            ];

            if ($staffId) {
                $updateData['MaNV'] = $staffId;
            }

            if ($note) {
                $updateData['GhiChu'] = trim(($lockedOrder->GhiChu ? $lockedOrder->GhiChu . " | " : "") . $note);
            }

            if ($newStatus === 'delivered' && $lockedOrder->PhuongThucThanhToan === 'COD') {
                $updateData['DaThanhToan'] = true;
            }

            $lockedOrder->update($updateData);

            return $lockedOrder;
        });
    }

    /**
     * Cancel order safely with atomic restocking.
     * @throws Exception
     */
    public function cancelOrder(DonHang $order, string $reason = 'Khách hàng hủy đơn'): bool
    {
        return DB::transaction(function () use ($order, $reason) {
            // Re-fetch order with lock
            $order = DonHang::where('MaDonHang', $order->MaDonHang)->lockForUpdate()->first();
            if (!$order) return false;

            if (in_array($order->TrangThaiDonHang, ['shipping', 'delivered', 'cancelled'], true)) {
                throw new Exception("Không thể hủy đơn hàng ở trạng thái '{$order->trang_thai_tieng_viet}'.");
            }

            // Restock items
            $orderItems = ChiTietDonHang::where('MaDonHang', $order->MaDonHang)->get();
            foreach ($orderItems as $item) {
                ChiTietSanPham::where('MaCTSP', $item->MaCTSP)->increment('SoLuong', $item->SoLuong);
            }

            // Restock voucher if used
            if ($order->MaCode) {
                MaGiamGia::where('MaCode', $order->MaCode)->increment('SoLuong', 1);
            }

            $order->update([
                'TrangThaiDonHang' => 'cancelled',
                'GhiChu' => trim(($order->GhiChu ? $order->GhiChu . " | " : "") . "Lý do hủy: " . $reason),
            ]);

            return true;
        });
    }
}

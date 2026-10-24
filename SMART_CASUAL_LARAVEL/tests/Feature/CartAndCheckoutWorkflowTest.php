<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ChiTietSanPham;
use App\Models\MaGiamGia;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\KhachHang;
use App\Models\GioHang;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class CartAndCheckoutWorkflowTest extends TestCase
{
    use DatabaseTransactions;
    public function test_cart_add_update_remove_operations(): void
    {
        $variant = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>', 5)->first();
        $this->assertNotNull($variant);

        $cartService = app(CartService::class);
        $cartService->clear();

        // 1. Add item
        $cartService->addItem($variant->MaCTSP, 2);
        $this->assertEquals(2, $cartService->getTotalQuantity());
        $this->assertEquals((float) $variant->GiaBan * 2, $cartService->getSubtotal());

        // 2. Update quantity
        $cartService->updateQuantity($variant->MaCTSP, 3);
        $this->assertEquals(3, $cartService->getTotalQuantity());

        // 3. Remove item
        $cartService->removeItem($variant->MaCTSP);
        $this->assertEquals(0, $cartService->getTotalQuantity());
    }

    public function test_checkout_atomic_workflow_and_stock_deduction(): void
    {
        $variant = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>', 10)->first();
        $this->assertNotNull($variant);
        $initialStock = $variant->SoLuong;

        $cartService = app(CartService::class);
        $cartService->clear();
        $cartService->addItem($variant->MaCTSP, 2);

        $checkoutService = app(CheckoutService::class);

        $order = $checkoutService->placeOrder([
            'receiver_name' => 'Nguyễn Mua Hàng',
            'receiver_phone' => '0912345678',
            'receiver_email' => 'muahang@gmail.com',
            'shipping_address' => '456 Lê Lợi, Quận 1, TP.HCM',
            'shipping_code' => 'HT',
            'payment_method' => 'COD',
            'voucher_code' => 'SAVE10',
        ]);

        $this->assertNotNull($order);
        $this->assertDatabaseHas('DON_HANG', ['MaDonHang' => $order->MaDonHang]);

        // Verify stock deducted
        $variant->refresh();
        $this->assertEquals($initialStock - 2, $variant->SoLuong);

        // Verify totals invariant
        $expectedSubtotal = (float) $variant->GiaBan * 2;
        $this->assertEquals($expectedSubtotal, (float) $order->TongTien);
        $this->assertEquals(30000, (float) $order->PhiVanChuyen); // HT is 30,000 VND
        $expectedDiscount = round($expectedSubtotal * 0.10, 2); // SAVE10 is 10%
        $this->assertEquals($expectedDiscount, (float) $order->GiamGia);
        $this->assertEquals(max(0, $expectedSubtotal + 30000 - $expectedDiscount), (float) $order->ThanhTien);

        // Test cancel order & restock
        $checkoutService->cancelOrder($order, 'Khách đổi ý');
        $order->refresh();
        $this->assertEquals('cancelled', $order->TrangThaiDonHang);

        $variant->refresh();
        $this->assertEquals($initialStock, $variant->SoLuong); // Restocked!
    }

    public function test_cannot_checkout_more_than_available_stock(): void
    {
        $variant = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>=', 5)->first();
        $this->assertNotNull($variant);

        $cartService = app(CartService::class);
        $cartService->clear();
        $cartService->addItem($variant->MaCTSP, 2);

        // Simulate another customer just bought out the stock concurrently
        $variant->update(['SoLuong' => 1]);

        $checkoutService = app(CheckoutService::class);

        $this->expectException(\Exception::class);
        $checkoutService->placeOrder([
            'receiver_name' => 'Khách Vượt Kho',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Địa chỉ test',
            'shipping_code' => 'HT',
        ]);
    }

    public function test_member_composite_cart_with_three_items_update_and_delete(): void
    {
        $customer = KhachHang::first();
        $this->assertNotNull($customer);

        $variants = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>=', 10)->take(3)->get();
        $this->assertCount(3, $variants);

        $cartService = app(CartService::class);

        // Login as customer
        $this->actingAs($customer, 'web');
        $cartService->clear();

        // 1. Add 3 distinct items
        $cartService->addItem($variants[0]->MaCTSP, 1);
        $cartService->addItem($variants[1]->MaCTSP, 2);
        $cartService->addItem($variants[2]->MaCTSP, 3);

        $items = GioHang::where('MaKH', $customer->MaKH)->get();
        $this->assertCount(3, $items);

        // 2. Update item 0 quantity to 5
        $cartService->updateQuantity($variants[0]->MaCTSP, 5);

        // Verify item 0 updated, while items 1 and 2 remain UNCHANGED
        $row0 = GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variants[0]->MaCTSP)->first();
        $row1 = GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variants[1]->MaCTSP)->first();
        $row2 = GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variants[2]->MaCTSP)->first();

        $this->assertEquals(5, $row0->SoLuong, 'Item 0 must be updated to 5');
        $this->assertEquals(2, $row1->SoLuong, 'Item 1 must remain 2');
        $this->assertEquals(3, $row2->SoLuong, 'Item 2 must remain 3');

        // 3. Remove item 1, verify item 0 and item 2 remain intact
        $cartService->removeItem($variants[1]->MaCTSP);
        $this->assertNull(GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variants[1]->MaCTSP)->first());
        $this->assertNotNull(GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variants[0]->MaCTSP)->first());
        $this->assertNotNull(GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $variants[2]->MaCTSP)->first());
    }

    public function test_two_members_with_same_variant_do_not_interfere(): void
    {
        $customers = KhachHang::take(2)->get();
        $this->assertCount(2, $customers);
        $userA = $customers[0];
        $userB = $customers[1];

        $variant = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>=', 10)->first();
        $this->assertNotNull($variant);

        $cartService = app(CartService::class);

        // User A adds 2
        $this->actingAs($userA, 'web');
        $cartService->clear();
        $cartService->addItem($variant->MaCTSP, 2);

        // User B adds 4
        $this->actingAs($userB, 'web');
        $cartService->clear();
        $cartService->addItem($variant->MaCTSP, 4);

        // User A updates to 5
        $this->actingAs($userA, 'web');
        $cartService->updateQuantity($variant->MaCTSP, 5);

        // Check in DB
        $itemA = GioHang::where('MaKH', $userA->MaKH)->where('MaCTSP', $variant->MaCTSP)->first();
        $itemB = GioHang::where('MaKH', $userB->MaKH)->where('MaCTSP', $variant->MaCTSP)->first();

        $this->assertEquals(5, $itemA->SoLuong);
        $this->assertEquals(4, $itemB->SoLuong, "User B's cart must not be affected by User A's update");
    }

    public function test_guest_cart_and_member_cart_merge_cleanly(): void
    {
        $customer = KhachHang::first();
        $this->assertNotNull($customer);

        $variants = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>=', 10)->take(2)->get();
        $v1 = $variants[0];
        $v2 = $variants[1];

        $cartService = app(CartService::class);

        // Member already has V1 in DB (qty 1)
        $this->actingAs($customer, 'web');
        $cartService->clear();
        $cartService->addItem($v1->MaCTSP, 1);

        // Logout -> Guest session adds V1 (qty 2) and V2 (qty 3)
        auth('web')->logout();
        session()->flush();
        $cartService->addItem($v1->MaCTSP, 2);
        $cartService->addItem($v2->MaCTSP, 3);

        // Merge guest cart into customer
        $cartService->mergeGuestCartToCustomer($customer->MaKH);

        // Verify merged results in DB for customer: V1 has 1+2=3, V2 has 3
        $row1 = GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $v1->MaCTSP)->first();
        $row2 = GioHang::where('MaKH', $customer->MaKH)->where('MaCTSP', $v2->MaCTSP)->first();

        $this->assertEquals(3, $row1->SoLuong);
        $this->assertEquals(3, $row2->SoLuong);
    }

    public function test_trigger_update_order_items_recalculates_both_old_and_new_orders(): void
    {
        // 1. Create two test orders
        $orderA = DonHang::create([
            'MaDonHang' => 'DH_TEST_A_' . rand(100, 999),
            'TenNguoiNhan' => 'Người Nhận A',
            'SoDienThoai' => '0901111111',
            'DiaChiGiao' => 'Địa chỉ A',
            'PhiVanChuyen' => 30000,
            'GiamGia' => 0,
            'TongSoLuong' => 0,
            'TongTien' => 0,
            'ThanhTien' => 30000,
        ]);

        $orderB = DonHang::create([
            'MaDonHang' => 'DH_TEST_B_' . rand(100, 999),
            'TenNguoiNhan' => 'Người Nhận B',
            'SoDienThoai' => '0902222222',
            'DiaChiGiao' => 'Địa chỉ B',
            'PhiVanChuyen' => 30000,
            'GiamGia' => 0,
            'TongSoLuong' => 0,
            'TongTien' => 0,
            'ThanhTien' => 30000,
        ]);

        $variants = ChiTietSanPham::where('TrangThai', true)->take(2)->get();
        $v1 = $variants[0];
        $v2 = $variants[1];

        // Add 2 items to Order A
        ChiTietDonHang::create([
            'MaDonHang' => $orderA->MaDonHang,
            'MaCTSP' => $v1->MaCTSP,
            'SoLuong' => 2,
            'DonGia' => 100000,
            'ThanhTien' => 200000,
        ]);

        ChiTietDonHang::create([
            'MaDonHang' => $orderA->MaDonHang,
            'MaCTSP' => $v2->MaCTSP,
            'SoLuong' => 1,
            'DonGia' => 150000,
            'ThanhTien' => 150000,
        ]);

        // Refresh Order A after trigger inserts
        $orderA->refresh();
        $this->assertEquals(3, $orderA->TongSoLuong);
        $this->assertEquals(350000, (float) $orderA->TongTien);
        $this->assertEquals(380000, (float) $orderA->ThanhTien);

        // Order B initially has 0 items
        $orderB->refresh();
        $this->assertEquals(0, $orderB->TongSoLuong);
        $this->assertEquals(0, (float) $orderB->TongTien);

        // Move Item 2 (v2) from Order A to Order B via UPDATE
        DB::table('CHI_TIET_DON_HANG')
            ->where('MaDonHang', $orderA->MaDonHang)
            ->where('MaCTSP', $v2->MaCTSP)
            ->update(['MaDonHang' => $orderB->MaDonHang]);

        // Refresh BOTH orders
        $orderA->refresh();
        $orderB->refresh();

        // Order A now only has Item 1 (qty 2, subtotal 200k, grand 230k)
        $this->assertEquals(2, $orderA->TongSoLuong);
        $this->assertEquals(200000, (float) $orderA->TongTien);
        $this->assertEquals(230000, (float) $orderA->ThanhTien);

        // Order B now has Item 2 (qty 1, subtotal 150k, grand 180k)
        $this->assertEquals(1, $orderB->TongSoLuong);
        $this->assertEquals(150000, (float) $orderB->TongTien);
        $this->assertEquals(180000, (float) $orderB->ThanhTien);
    }

    public function test_freeship_voucher_and_shipping_discount_rules(): void
    {
        $shippingMethod = \App\Models\PhuongThucVanChuyen::where('MaPTVC', 'HT')->first();
        $this->assertNotNull($shippingMethod);
        $shippingFee = (float) $shippingMethod->GiaVanChuyen; // 30,000

        // 1. FREESHIP voucher (GiaTri = 0) must discount full shipping fee
        $freeship = MaGiamGia::where('MaCode', 'FREESHIP')->first();
        $this->assertNotNull($freeship);
        $this->assertEquals('shipping', $freeship->LoaiGiamGia);
        $discount = $freeship->calculateDiscount(200000, $shippingFee);
        $this->assertEquals($shippingFee, $discount, 'FREESHIP with GiaTri=0 must discount 100% of shipping fee');

        // 2. Partial shipping voucher (e.g. GiaTri = 15,000)
        $partialShip = new MaGiamGia([
            'MaCode' => 'SHIP15',
            'LoaiGiamGia' => 'shipping',
            'GiaTri' => 15000,
            'SoLuong' => 5,
            'TrangThai' => true,
        ]);
        $discountPartial = $partialShip->calculateDiscount(200000, $shippingFee);
        $this->assertEquals(15000, $discountPartial);

        // 3. Discount cannot make grand total negative
        $hugeCash = new MaGiamGia([
            'MaCode' => 'HUGE500',
            'LoaiGiamGia' => 'cash',
            'GiaTri' => 500000,
            'SoLuong' => 5,
            'TrangThai' => true,
        ]);
        $discountCash = $hugeCash->calculateDiscount(100000, $shippingFee);
        $this->assertLessThanOrEqual(100000 + $shippingFee, $discountCash);
    }

    public function test_cart_out_of_stock_clamp_and_zero_prevention(): void
    {
        $variant = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>=', 5)->first();
        $this->assertNotNull($variant);

        $cartService = app(CartService::class);
        $cartService->clear();

        // Add 2 items
        $this->assertTrue($cartService->addItem($variant->MaCTSP, 2));

        // Simulate variant becoming out of stock (SoLuong = 0)
        $variant->update(['SoLuong' => 0]);

        // Attempt to update quantity must return false and NOT set SoLuong to 0
        $result = $cartService->updateQuantity($variant->MaCTSP, 3);
        $this->assertFalse($result);

        // Check guest cart session does not store quantity 0
        $items = $cartService->getItems();
        foreach ($items as $item) {
            $this->assertGreaterThan(0, $item['quantity'], 'Cart item quantity must never be 0');
        }

        // Test with stock = 2 and requested = 5 -> should cap at available stock (2), not 0
        $variant->update(['SoLuong' => 2]);
        $this->assertTrue($cartService->updateQuantity($variant->MaCTSP, 5));
        $this->assertEquals(2, $cartService->getTotalQuantity());
    }

    public function test_order_state_transition_guard_rules_and_invalid_block(): void
    {
        $checkoutService = app(CheckoutService::class);

        $order = DonHang::create([
            'MaDonHang' => 'DH_TRANS_' . rand(1000, 9999),
            'TenNguoiNhan' => 'Test Transitions',
            'SoDienThoai' => '0901234567',
            'DiaChiGiao' => '123 HCM',
            'PhiVanChuyen' => 20000,
            'TongSoLuong' => 1,
            'TongTien' => 100000,
            'ThanhTien' => 120000,
            'PhuongThucThanhToan' => 'COD',
            'TrangThaiDonHang' => 'pending',
            'TrangThai' => true,
        ]);

        // 1. Invalid: pending -> delivered directly is forbidden
        try {
            $checkoutService->updateOrderStatus($order, 'delivered');
            $this->fail('Should not allow pending -> delivered directly');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Không thể chuyển trạng thái', $e->getMessage());
        }

        // 2. Valid lifecycle: pending -> confirmed -> packing -> shipping -> delivered
        $order = $checkoutService->updateOrderStatus($order, 'confirmed');
        $this->assertEquals('confirmed', $order->TrangThaiDonHang);

        $order = $checkoutService->updateOrderStatus($order, 'packing');
        $this->assertEquals('packing', $order->TrangThaiDonHang);

        $order = $checkoutService->updateOrderStatus($order, 'shipping');
        $this->assertEquals('shipping', $order->TrangThaiDonHang);

        $order = $checkoutService->updateOrderStatus($order, 'delivered');
        $this->assertEquals('delivered', $order->TrangThaiDonHang);
        $this->assertTrue($order->DaThanhToan, 'COD order must be marked paid on delivery');

        // 3. Invalid: delivered -> pending or cancelled is forbidden
        try {
            $checkoutService->updateOrderStatus($order, 'pending');
            $this->fail('Should not allow delivered -> pending');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Không thể chuyển trạng thái', $e->getMessage());
        }

        try {
            $checkoutService->updateOrderStatus($order, 'cancelled');
            $this->fail('Should not allow delivered -> cancelled');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Không thể', $e->getMessage());
        }
    }

    public function test_checkout_backend_idempotency_prevents_duplicate_orders(): void
    {
        $variant = ChiTietSanPham::where('TrangThai', true)->where('SoLuong', '>=', 10)->first();
        $this->assertNotNull($variant);
        $initialStock = $variant->SoLuong;

        $cartService = app(CartService::class);
        $cartService->clear();
        $cartService->addItem($variant->MaCTSP, 2);

        $checkoutService = app(CheckoutService::class);

        $idempotencyKey = 'IDEM_KEY_' . uniqid();
        $orderData = [
            'receiver_name' => 'Nguyễn Idempotent',
            'receiver_phone' => '0911222333',
            'shipping_address' => '789 Test Address',
            'shipping_code' => 'HT',
            'payment_method' => 'COD',
            'idempotency_key' => $idempotencyKey,
        ];

        // 1. First submission -> creates order
        $order1 = $checkoutService->placeOrder($orderData, $idempotencyKey);
        $this->assertNotNull($order1);

        $variant->refresh();
        $this->assertEquals($initialStock - 2, $variant->SoLuong);

        // 2. Second submission with SAME idempotency key -> returns existing order, NO double stock deduction!
        $order2 = $checkoutService->placeOrder($orderData, $idempotencyKey);
        $this->assertEquals($order1->MaDonHang, $order2->MaDonHang);

        $variant->refresh();
        $this->assertEquals($initialStock - 2, $variant->SoLuong, 'Stock must NOT be deducted twice');

        // 3. Same key with DIFFERENT payload must be rejected
        $tamperedData = $orderData;
        $tamperedData['receiver_name'] = 'Different Person';
        $this->expectException(\Exception::class);
        $checkoutService->placeOrder($tamperedData, $idempotencyKey);
    }
}

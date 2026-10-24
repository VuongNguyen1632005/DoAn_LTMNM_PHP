<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\KhachHang;
use App\Models\DonHang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthenticationAndAuthorizationTest extends TestCase
{
    use DatabaseTransactions;
    public function test_customer_registration_with_password_hashing(): void
    {
        $unique = rand(1000, 9999);
        $email = "testuser{$unique}@gmail.com";
        $username = "user{$unique}";

        $response = $this->post('/register', [
            'HoTen' => 'Nguyễn Kiểm Thử',
            'Email' => $email,
            'TenDangNhap' => $username,
            'MatKhau' => 'secret123',
            'MatKhau_confirmation' => 'secret123',
            'SoDienThoai' => '0987654321',
            'DiaChi' => 'Hồ Chí Minh',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs(KhachHang::where('Email', $email)->first(), 'web');

        $created = KhachHang::where('Email', $email)->first();
        $this->assertNotNull($created);
        $this->assertTrue(Hash::check('secret123', $created->MatKhau));
    }

    public function test_customer_login_and_logout(): void
    {
        $customer = KhachHang::first();
        $this->assertNotNull($customer);

        // Login
        $response = $this->post('/login', [
            'login' => $customer->Email,
            'password' => 'password123',
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($customer, 'web');

        // Logout
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest('web');
    }

    public function test_user_cannot_access_other_users_order_idor_protection(): void
    {
        $userA = KhachHang::where('MaKH', 'KH001')->first();
        $userB = KhachHang::where('MaKH', 'KH002')->first();

        $this->assertNotNull($userA);
        $this->assertNotNull($userB);

        // Find an order belonging to User A
        $orderA = DonHang::where('MaKH', $userA->MaKH)->first();
        $this->assertNotNull($orderA);

        // User B tries to view User A's order -> should be 403 Forbidden
        $response = $this->actingAs($userB, 'web')->get('/account/orders/' . $orderA->MaDonHang);
        $response->assertStatus(403);
    }

    public function test_guest_cannot_view_or_manipulate_any_order(): void
    {
        $order = DonHang::first();
        $this->assertNotNull($order);

        // 1. Guest cannot view order detail
        $response = $this->get('/account/orders/' . $order->MaDonHang);
        $response->assertRedirect('/login');

        // 2. Guest cannot cancel order
        $response = $this->post('/account/orders/' . $order->MaDonHang . '/cancel');
        $response->assertRedirect('/login');

        // 3. Guest cannot confirm order received
        $response = $this->post('/account/orders/' . $order->MaDonHang . '/confirm-received');
        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_cancel_other_users_order(): void
    {
        $userA = KhachHang::where('MaKH', 'KH001')->first();
        $userB = KhachHang::where('MaKH', 'KH002')->first();

        $orderA = DonHang::where('MaKH', $userA->MaKH)->first();
        $this->assertNotNull($orderA);

        // User B attempts direct POST cancel on User A's order
        $response = $this->actingAs($userB, 'web')->post('/account/orders/' . $orderA->MaDonHang . '/cancel');
        $response->assertStatus(403);

        // User B attempts direct POST confirm-received on User A's order
        $response = $this->actingAs($userB, 'web')->post('/account/orders/' . $orderA->MaDonHang . '/confirm-received');
        $response->assertStatus(403);
    }

    public function test_customer_can_cancel_own_pending_order_but_not_invalid_status(): void
    {
        $userA = KhachHang::where('MaKH', 'KH001')->first();
        $this->assertNotNull($userA);

        // Create a test pending order for User A
        $order = DonHang::create([
            'MaDonHang' => 'DH_CANCEL_' . rand(100, 999),
            'MaKH' => $userA->MaKH,
            'TenNguoiNhan' => $userA->HoTen,
            'SoDienThoai' => '0901234567',
            'DiaChiGiao' => '123 Đường Test',
            'TongSoLuong' => 1,
            'TongTien' => 100000,
            'PhiVanChuyen' => 30000,
            'ThanhTien' => 130000,
            'TrangThaiDonHang' => 'pending',
            'TrangThai' => true,
        ]);

        // Customer cancels own pending order -> Should succeed
        $response = $this->actingAs($userA, 'web')->post('/account/orders/' . $order->MaDonHang . '/cancel');
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('cancelled', $order->TrangThaiDonHang);

        // Customer tries to cancel the already cancelled order again -> Should fail with error message
        $response = $this->actingAs($userA, 'web')->post('/account/orders/' . $order->MaDonHang . '/cancel');
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_login_rate_limiting_triggers_429(): void
    {
        $fakeLogin = 'attacker_' . rand(1000, 9999) . '@test.com';

        // Send 5 failed login attempts
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/login', [
                'login' => $fakeLogin,
                'password' => 'wrong_password',
            ]);
        }

        // 6th attempt must receive HTTP 429 Too Many Requests
        $response = $this->post('/login', [
            'login' => $fakeLogin,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(429);
    }
}

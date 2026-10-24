<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\NhanVien;
use App\Models\KhachHang;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminCrudAndMiddlewareTest extends TestCase
{
    use DatabaseTransactions;
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = KhachHang::first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer, 'web')->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_staff_can_login_and_access_admin_dashboard(): void
    {
        $staff = NhanVien::where('TenDangNhap', 'admin')->first();
        $this->assertNotNull($staff);

        $response = $this->actingAs($staff, 'admin')->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Bảng điều khiển');
    }

    public function test_admin_category_crud_workflow(): void
    {
        $staff = NhanVien::where('TenDangNhap', 'admin')->first();
        $testCatCode = 'TEST' . rand(10, 99);

        // 1. Create Category
        $response = $this->actingAs($staff, 'admin')->post('/admin/categories', [
            'MaDanhMuc' => $testCatCode,
            'TenDanhMuc' => 'Danh mục thử nghiệm',
        ]);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('DANH_MUC', ['MaDanhMuc' => $testCatCode]);

        // 2. Update Category
        $response = $this->actingAs($staff, 'admin')->put('/admin/categories/' . $testCatCode, [
            'TenDanhMuc' => 'Danh mục đã sửa',
            'TrangThai' => 1,
        ]);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('DANH_MUC', [
            'MaDanhMuc' => $testCatCode,
            'TenDanhMuc' => 'Danh mục đã sửa',
        ]);

        // 3. Delete Category
        $response = $this->actingAs($staff, 'admin')->delete('/admin/categories/' . $testCatCode);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('DANH_MUC', ['MaDanhMuc' => $testCatCode]);
    }

    public function test_admin_product_crud_and_soft_delete(): void
    {
        $staff = NhanVien::where('TenDangNhap', 'admin')->first();
        $testProductCode = 'TESTSP' . rand(100, 999);

        // 1. Validation error when required fields are missing
        $response = $this->actingAs($staff, 'admin')->post('/admin/products', []);
        $response->assertSessionHasErrors(['MaSP', 'TenSanPham', 'MaDanhMuc', 'GiaBan', 'SoLuong', 'MaKichThuoc']);

        // 2. Create Product
        $response = $this->actingAs($staff, 'admin')->post('/admin/products', [
            'MaSP' => $testProductCode,
            'MaDanhMuc' => 'A',
            'TenSanPham' => 'Áo Thun Kiểm Thử Tự Động',
            'GiaBan' => 200000,
            'SoLuong' => 50,
            'MaKichThuoc' => 'L',
            'MoTa' => 'Mô tả test tự động',
        ]);
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('SAN_PHAM', ['MaSP' => $testProductCode, 'TrangThai' => 1]);

        // 3. Soft Delete / Hide Product
        $response = $this->actingAs($staff, 'admin')->delete('/admin/products/' . $testProductCode);
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('SAN_PHAM', ['MaSP' => $testProductCode, 'TrangThai' => 0]);

        // 4. Restore Product
        $response = $this->actingAs($staff, 'admin')->post('/admin/products/' . $testProductCode . '/restore');
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('SAN_PHAM', ['MaSP' => $testProductCode, 'TrangThai' => 1]);
    }

    public function test_staff_blocked_from_manager_revenue_and_mutations(): void
    {
        $staff = NhanVien::where('ChucVu', 'staff')->first();
        $this->assertNotNull($staff);

        // 1. Staff blocked from Revenue
        $response = $this->actingAs($staff, 'admin')->get('/admin/revenue');
        $response->assertStatus(403);

        // 2. Staff blocked from creating category
        $response = $this->actingAs($staff, 'admin')->post('/admin/categories', [
            'MaDanhMuc' => 'STF' . rand(10, 99),
            'TenDanhMuc' => 'Staff Category Attempt',
        ]);
        $response->assertStatus(403);

        // 3. Staff blocked from creating product
        $response = $this->actingAs($staff, 'admin')->post('/admin/products', [
            'MaSP' => 'STFSP' . rand(100, 999),
            'MaDanhMuc' => 'A',
            'TenSanPham' => 'Staff Product Attempt',
            'GiaBan' => 100000,
            'SoLuong' => 10,
            'MaKichThuoc' => 'L',
        ]);
        $response->assertStatus(403);

        // 4. Staff blocked from deleting product
        $response = $this->actingAs($staff, 'admin')->delete('/admin/products/A01');
        $response->assertStatus(403);
    }

    public function test_staff_allowed_to_view_and_process_orders(): void
    {
        $staff = NhanVien::where('ChucVu', 'staff')->first();
        $this->assertNotNull($staff);

        // Staff can view orders index
        $response = $this->actingAs($staff, 'admin')->get('/admin/orders');
        $response->assertStatus(200);

        // Staff can update order status
        $order = \App\Models\DonHang::first();
        $this->assertNotNull($order);

        $response = $this->actingAs($staff, 'admin')->post('/admin/orders/' . $order->MaDonHang . '/status', [
            'status' => 'confirmed',
            'note' => 'Staff confirmed this order',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_manager_can_access_revenue_and_all_management(): void
    {
        $manager = NhanVien::where('ChucVu', 'manager')->first();
        $this->assertNotNull($manager);

        // Manager can view revenue
        $response = $this->actingAs($manager, 'admin')->get('/admin/revenue');
        $response->assertStatus(200);
        $response->assertSee('Doanh thu');
    }
}

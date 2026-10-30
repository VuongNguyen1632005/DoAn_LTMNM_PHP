<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\NhanVien;
use App\Models\KhachHang;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

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

    public function test_manager_can_create_and_manage_employee_accounts(): void
    {
        $manager = NhanVien::where('ChucVu', 'manager')->first();
        $this->assertNotNull($manager);

        $response = $this->actingAs($manager, 'admin')->post('/admin/employees', [
            'TenDangNhap' => 'manager-created-staff',
            'HoTen' => 'Nhân viên kiểm thử',
            'ChucVu' => 'staff',
            'password' => 'test-password-123',
            'password_confirmation' => 'test-password-123',
        ]);
        $response->assertRedirect('/admin/employees');

        $employee = NhanVien::where('TenDangNhap', 'manager-created-staff')->first();
        $this->assertNotNull($employee);
        $this->assertTrue(Hash::check('test-password-123', $employee->MatKhau));
        $this->assertNotSame('test-password-123', $employee->MatKhau);

        $response = $this->actingAs($manager, 'admin')->patch('/admin/employees/' . $employee->MaNV . '/status');
        $response->assertRedirect();
        $this->assertDatabaseHas('NHAN_VIEN', ['MaNV' => $employee->MaNV, 'TrangThai' => 0]);

        $response = $this->actingAs($manager, 'admin')->patch('/admin/employees/' . $manager->MaNV . '/status');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('NHAN_VIEN', ['MaNV' => $manager->MaNV, 'TrangThai' => 1]);
    }

    public function test_manager_can_manage_vouchers_and_validation_rejects_invalid_percent(): void
    {
        $manager = NhanVien::where('ChucVu', 'manager')->first();
        $this->assertNotNull($manager);

        $response = $this->actingAs($manager, 'admin')->post('/admin/vouchers', [
            'MaCode' => 'test-percent-10',
            'LoaiGiamGia' => 'percent',
            'GiaTri' => 10,
            'SoLuong' => 5,
        ]);
        $response->assertRedirect('/admin/vouchers');
        $this->assertDatabaseHas('MA_GIAM_GIA', [
            'MaCode' => 'TEST-PERCENT-10',
            'LoaiGiamGia' => 'percent',
            'GiaTri' => 10,
            'SoLuong' => 5,
            'TrangThai' => 1,
        ]);

        $response = $this->actingAs($manager, 'admin')->put('/admin/vouchers/TEST-PERCENT-10', [
            'LoaiGiamGia' => 'percent',
            'GiaTri' => 150,
            'SoLuong' => 5,
            'TrangThai' => 1,
        ]);
        $response->assertSessionHasErrors('GiaTri');

        $response = $this->actingAs($manager, 'admin')->patch('/admin/vouchers/TEST-PERCENT-10/status');
        $response->assertRedirect();
        $this->assertDatabaseHas('MA_GIAM_GIA', ['MaCode' => 'TEST-PERCENT-10', 'TrangThai' => 0]);
    }

    public function test_staff_cannot_access_employee_or_voucher_management(): void
    {
        $staff = NhanVien::where('ChucVu', 'staff')->first();
        $this->assertNotNull($staff);

        $this->actingAs($staff, 'admin')->get('/admin/employees')->assertStatus(403);
        $this->actingAs($staff, 'admin')->get('/admin/vouchers')->assertStatus(403);
        $this->actingAs($staff, 'admin')->post('/admin/employees', [])->assertStatus(403);
        $this->actingAs($staff, 'admin')->post('/admin/vouchers', [])->assertStatus(403);
    }
}

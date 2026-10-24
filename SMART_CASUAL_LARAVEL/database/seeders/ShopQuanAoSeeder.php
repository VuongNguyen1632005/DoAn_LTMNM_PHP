<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\DanhMuc;
use App\Models\KichThuoc;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use App\Models\HinhAnhSanPham;
use App\Models\KhachHang;
use App\Models\NhanVien;
use App\Models\MaGiamGia;
use App\Models\PhuongThucVanChuyen;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\GioHang;
use App\Models\DanhGia;

class ShopQuanAoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixturePath = dirname(__DIR__, 3) . '/PHP_COUCHDB/database/couchdb/seeds/retail_order_delivery.json';
        if (!file_exists($fixturePath)) {
            $this->command->error("Fixture file not found: {$fixturePath}");
            return;
        }

        $json = json_decode(file_get_contents($fixturePath), true);
        $docs = $json['docs'] ?? [];

        $this->command->info("Loaded " . count($docs) . " raw documents from fixture.");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DanhGia::truncate();
        ChiTietDonHang::truncate();
        DonHang::truncate();
        GioHang::truncate();
        PhuongThucVanChuyen::truncate();
        MaGiamGia::truncate();
        NhanVien::truncate();
        KhachHang::truncate();
        HinhAnhSanPham::truncate();
        ChiTietSanPham::truncate();
        SanPham::truncate();
        KichThuoc::truncate();
        DanhMuc::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. DANH_MUC
        $categories = [
            ['MaDanhMuc' => 'A', 'TenDanhMuc' => 'Áo', 'TrangThai' => true],
            ['MaDanhMuc' => 'Q', 'TenDanhMuc' => 'Quần', 'TrangThai' => true],
            ['MaDanhMuc' => 'G', 'TenDanhMuc' => 'Giày', 'TrangThai' => true],
        ];
        foreach ($categories as $cat) {
            DanhMuc::updateOrCreate(['MaDanhMuc' => $cat['MaDanhMuc']], $cat);
        }
        $this->command->info("Seeded DANH_MUC: " . count($categories));

        // 2. KICH_THUOC
        // Collect all distinct sizes
        $sizesMap = [];
        $standardSizes = ['S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        foreach ($standardSizes as $s) {
            $sizesMap[$s] = $s;
        }
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') === 'product' && !empty($doc['variants'])) {
                foreach ($doc['variants'] as $v) {
                    $sName = (string) $v['size'];
                    $sizesMap[$sName] = $sName;
                }
            }
        }
        foreach ($sizesMap as $code => $name) {
            KichThuoc::updateOrCreate(['MaKichThuoc' => (string) $code], [
                'MaKichThuoc' => (string) $code,
                'TenKichThuoc' => (string) $name,
                'TrangThai' => true,
            ]);
        }
        $this->command->info("Seeded KICH_THUOC: " . count($sizesMap));

        // 3. SAN_PHAM, CHI_TIET_SP, HINH_ANH_SP
        $productCount = 0;
        $variantCount = 0;
        $imageCount = 0;

        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'product') continue;

            $pId = $doc['legacy_id'];
            $catCode = $doc['category']['code'] ?? 'A';

            SanPham::updateOrCreate(['MaSP' => $pId], [
                'MaSP' => $pId,
                'MaDanhMuc' => $catCode,
                'TenSanPham' => $doc['name'] ?? '',
                'MoTa' => $doc['description'] ?? '',
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $productCount++;

            // Variants
            foreach ($doc['variants'] ?? [] as $v) {
                ChiTietSanPham::updateOrCreate(['MaCTSP' => $v['variant_id']], [
                    'MaCTSP' => $v['variant_id'],
                    'MaSP' => $pId,
                    'MaKichThuoc' => (string) $v['size'],
                    'GiaBan' => (float) ($v['price'] ?? 0),
                    'SoLuong' => (int) ($v['stock'] ?? 0),
                    'TrangThai' => (bool) ($v['active'] ?? true),
                ]);
                $variantCount++;
            }

            // Images
            foreach ($doc['images'] ?? [] as $img) {
                HinhAnhSanPham::updateOrCreate(['MaHinhAnh' => $img['legacy_id']], [
                    'MaHinhAnh' => $img['legacy_id'],
                    'MaSP' => $pId,
                    'DuongDan' => $img['path'] ?? '',
                    'LaAnhChinh' => (bool) ($img['is_primary'] ?? false),
                    'TrangThai' => (bool) ($img['active'] ?? true),
                ]);
                $imageCount++;
            }
        }
        $this->command->info("Seeded SAN_PHAM: {$productCount}, CHI_TIET_SP: {$variantCount}, HINH_ANH_SP: {$imageCount}");

        // 4. KHACH_HANG
        $customerCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'customer') continue;
            $cId = $doc['legacy_id'];
            KhachHang::updateOrCreate(['MaKH' => $cId], [
                'MaKH' => $cId,
                'TenDangNhap' => $doc['auth']['username'] ?? ($cId . '@gmail.com'),
                'MatKhau' => Hash::make('password123'), // Secure demo password
                'HoTen' => $doc['profile']['name'] ?? 'Khách Hàng',
                'SoDienThoai' => $doc['profile']['phone'] ?? '0901234567',
                'Email' => $doc['profile']['email'] ?? ($cId . '@gmail.com'),
                'DiaChi' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $customerCount++;
        }
        $this->command->info("Seeded KHACH_HANG: {$customerCount}");

        // 5. NHAN_VIEN
        $staffCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'staff') continue;
            $sId = $doc['legacy_id'];
            $username = $doc['auth']['username'] ?? ($sId === 'NV001' ? 'admin' : 'staff');
            $pass = $username === 'admin' ? 'admin123' : 'staff123';
            NhanVien::updateOrCreate(['MaNV' => $sId], [
                'MaNV' => $sId,
                'TenDangNhap' => $username,
                'MatKhau' => Hash::make($pass), // Secure demo password
                'HoTen' => $doc['profile']['name'] ?? 'Nhân Viên',
                'ChucVu' => $doc['role'] ?? 'staff',
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $staffCount++;
        }
        $this->command->info("Seeded NHAN_VIEN: {$staffCount}");

        // 6. MA_GIAM_GIA
        $voucherCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'voucher') continue;
            MaGiamGia::updateOrCreate(['MaCode' => $doc['code']], [
                'MaCode' => $doc['code'],
                'GiaTri' => (float) ($doc['value'] ?? 0),
                'LoaiGiamGia' => $doc['discount_type'] ?? 'cash',
                'SoLuong' => (int) ($doc['remaining_quantity'] ?? 10),
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $voucherCount++;
        }
        $this->command->info("Seeded MA_GIAM_GIA: {$voucherCount}");

        // 7. PHUONG_THUC_VAN_CHUYEN
        $shippingCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'shipping_method') continue;
            PhuongThucVanChuyen::updateOrCreate(['MaPTVC' => $doc['code']], [
                'MaPTVC' => $doc['code'],
                'TenPTVC' => $doc['name'] ?? '',
                'GiaVanChuyen' => (float) ($doc['fee'] ?? 0),
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $shippingCount++;
        }
        $this->command->info("Seeded PHUONG_THUC_VAN_CHUYEN: {$shippingCount}");

        // 8. DON_HANG and CHI_TIET_DON_HANG
        $orderCount = 0;
        $orderItemCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'order') continue;
            $oId = $doc['legacy_id'];
            $custId = $doc['customer']['customer_id'] ?? null;
            $shippingCode = $doc['shipping']['method_code'] ?? ($doc['shipping']['code'] ?? 'HT');
            $voucherCode = $doc['discount']['code'] ?? null;

            DonHang::updateOrCreate(['MaDonHang' => $oId], [
                'MaDonHang' => $oId,
                'MaKH' => $custId,
                'MaNV' => $doc['assigned_staff_id'] ?? null,
                'TenNguoiNhan' => $doc['receiver']['name'] ?? 'Khách Hàng',
                'SoDienThoai' => $doc['receiver']['phone'] ?? '0901234567',
                'Email' => $doc['receiver']['email'] ?? null,
                'MaCode' => $voucherCode,
                'MaPTVC' => $shippingCode,
                'NgayDat' => $doc['ordered_at'] ? date('Y-m-d H:i:s', strtotime($doc['ordered_at'])) : now(),
                'DiaChiGiao' => $doc['receiver']['address'] ?? '123 Đường Số 1, TP.HCM',
                'GhiChu' => $doc['note'] ?? null,
                'TongSoLuong' => (int) ($doc['totals']['total_quantity'] ?? 1),
                'TongTien' => (float) ($doc['totals']['subtotal'] ?? 0),
                'GiamGia' => (float) ($doc['totals']['discount_amount'] ?? 0),
                'PhiVanChuyen' => (float) ($doc['totals']['shipping_fee'] ?? 0),
                'ThanhTien' => (float) ($doc['totals']['grand_total'] ?? 0),
                'PhuongThucThanhToan' => strtoupper($doc['payment']['method'] ?? 'COD'),
                'DaThanhToan' => (bool) ($doc['payment']['paid'] ?? false),
                'TrangThaiDonHang' => $doc['status'] ?? 'pending',
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $orderCount++;

            foreach ($doc['items'] ?? [] as $item) {
                ChiTietDonHang::updateOrCreate([
                    'MaDonHang' => $oId,
                    'MaCTSP' => $item['variant_id'],
                ], [
                    'MaDonHang' => $oId,
                    'MaCTSP' => $item['variant_id'],
                    'SoLuong' => (int) ($item['quantity'] ?? 1),
                    'DonGia' => (float) ($item['unit_price'] ?? 0),
                    'ThanhTien' => (float) ($item['line_total'] ?? 0),
                    'TrangThai' => true,
                ]);
                $orderItemCount++;
            }
        }
        $this->command->info("Seeded DON_HANG: {$orderCount}, CHI_TIET_DON_HANG: {$orderItemCount}");

        // 9. DANH_GIA
        $reviewCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'review') continue;
            $rId = $doc['legacy_id'];
            DanhGia::updateOrCreate(['MaDanhGia' => $rId], [
                'MaDanhGia' => $rId,
                'MaSP' => $doc['product_id'] ?? '',
                'MaKH' => $doc['customer_id'] ?? null,
                'TenKhachHang' => $doc['reviewer_name'] ?? 'Khách Mua',
                'SoSao' => (int) ($doc['rating'] ?? 5),
                'NoiDung' => $doc['content'] ?? '',
                'TrangThai' => (bool) ($doc['active'] ?? true),
            ]);
            $reviewCount++;
        }
        $this->command->info("Seeded DANH_GIA: {$reviewCount}");

        // 10. GIO_HANG
        $cartCount = 0;
        foreach ($docs as $doc) {
            if (($doc['type'] ?? '') !== 'cart') continue;
            $cId = $doc['customer_id'] ?? '';
            if (!$cId) continue;
            foreach ($doc['items'] ?? [] as $item) {
                GioHang::updateOrCreate([
                    'MaKH' => $cId,
                    'MaCTSP' => $item['variant_id'],
                ], [
                    'MaKH' => $cId,
                    'MaCTSP' => $item['variant_id'],
                    'SoLuong' => (int) ($item['quantity'] ?? 1),
                ]);
                $cartCount++;
            }
        }
        $this->command->info("Seeded GIO_HANG: {$cartCount}");

        $this->command->info("=== SEEDING COMPLETED SUCCESSFULLY ===");
    }
}

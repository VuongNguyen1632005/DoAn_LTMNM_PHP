<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. DANH_MUC
        Schema::create('DANH_MUC', function (Blueprint $table) {
            $table->string('MaDanhMuc', 10)->primary();
            $table->string('TenDanhMuc', 100);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();
        });

        // 2. KICH_THUOC
        Schema::create('KICH_THUOC', function (Blueprint $table) {
            $table->string('MaKichThuoc', 10)->primary();
            $table->string('TenKichThuoc', 50);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();
        });

        // 3. SAN_PHAM
        Schema::create('SAN_PHAM', function (Blueprint $table) {
            $table->string('MaSP', 20)->primary();
            $table->string('MaDanhMuc', 10);
            $table->string('TenSanPham', 200);
            $table->text('MoTa')->nullable();
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();

            $table->foreign('MaDanhMuc')
                ->references('MaDanhMuc')
                ->on('DANH_MUC')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 4. CHI_TIET_SP
        Schema::create('CHI_TIET_SP', function (Blueprint $table) {
            $table->string('MaCTSP', 30)->primary();
            $table->string('MaSP', 20);
            $table->string('MaKichThuoc', 10);
            $table->decimal('GiaBan', 18, 2)->default(0);
            $table->unsignedInteger('SoLuong')->default(0);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();

            $table->unique(['MaSP', 'MaKichThuoc'], 'uq_sp_kichthuoc');

            $table->foreign('MaSP')
                ->references('MaSP')
                ->on('SAN_PHAM')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('MaKichThuoc')
                ->references('MaKichThuoc')
                ->on('KICH_THUOC')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 5. HINH_ANH_SP
        Schema::create('HINH_ANH_SP', function (Blueprint $table) {
            $table->string('MaHinhAnh', 30)->primary();
            $table->string('MaSP', 20);
            $table->string('DuongDan', 255);
            $table->boolean('LaAnhChinh')->default(false);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();

            $table->foreign('MaSP')
                ->references('MaSP')
                ->on('SAN_PHAM')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 6. KHACH_HANG
        Schema::create('KHACH_HANG', function (Blueprint $table) {
            $table->string('MaKH', 20)->primary();
            $table->string('TenDangNhap', 100)->unique();
            $table->string('MatKhau', 255);
            $table->string('HoTen', 100);
            $table->string('SoDienThoai', 20)->nullable();
            $table->string('Email', 100)->nullable()->unique();
            $table->string('DiaChi', 255)->nullable();
            $table->boolean('TrangThai')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 7. NHAN_VIEN
        Schema::create('NHAN_VIEN', function (Blueprint $table) {
            $table->string('MaNV', 20)->primary();
            $table->string('TenDangNhap', 100)->unique();
            $table->string('MatKhau', 255);
            $table->string('HoTen', 100);
            $table->string('ChucVu', 50)->default('staff'); // 'manager' | 'staff'
            $table->boolean('TrangThai')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 8. MA_GIAM_GIA
        Schema::create('MA_GIAM_GIA', function (Blueprint $table) {
            $table->string('MaCode', 50)->primary();
            $table->decimal('GiaTri', 18, 2)->default(0);
            $table->string('LoaiGiamGia', 20)->default('cash'); // 'percent' | 'cash' | 'shipping'
            $table->integer('SoLuong')->default(0);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();
        });

        // 9. PHUONG_THUC_VAN_CHUYEN
        Schema::create('PHUONG_THUC_VAN_CHUYEN', function (Blueprint $table) {
            $table->string('MaPTVC', 20)->primary();
            $table->string('TenPTVC', 100);
            $table->decimal('GiaVanChuyen', 18, 2)->default(0);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();
        });

        // 10. GIO_HANG
        Schema::create('GIO_HANG', function (Blueprint $table) {
            $table->string('MaKH', 20);
            $table->string('MaCTSP', 30);
            $table->integer('SoLuong')->default(1);
            $table->timestamps();

            $table->primary(['MaKH', 'MaCTSP']);

            $table->foreign('MaKH')
                ->references('MaKH')
                ->on('KHACH_HANG')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('MaCTSP')
                ->references('MaCTSP')
                ->on('CHI_TIET_SP')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 11. DON_HANG
        Schema::create('DON_HANG', function (Blueprint $table) {
            $table->string('MaDonHang', 30)->primary();
            $table->string('MaKH', 20)->nullable();
            $table->string('MaNV', 20)->nullable();
            $table->string('TenNguoiNhan', 100);
            $table->string('SoDienThoai', 20);
            $table->string('Email', 100)->nullable();
            $table->string('MaCode', 50)->nullable();
            $table->string('MaPTVC', 20)->nullable();
            $table->dateTime('NgayDat')->useCurrent();
            $table->dateTime('NgayGiaoDuKien')->nullable();
            $table->string('DiaChiGiao', 255);
            $table->text('GhiChu')->nullable();
            $table->integer('TongSoLuong')->default(0);
            $table->decimal('TongTien', 18, 2)->default(0);
            $table->decimal('GiamGia', 18, 2)->default(0);
            $table->decimal('PhiVanChuyen', 18, 2)->default(0);
            $table->decimal('ThanhTien', 18, 2)->default(0);
            $table->string('PhuongThucThanhToan', 50)->default('COD');
            $table->boolean('DaThanhToan')->default(false);
            $table->string('TrangThaiDonHang', 50)->default('pending'); // 'pending', 'confirmed', 'packing', 'shipping', 'delivered', 'cancelled'
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();

            $table->foreign('MaKH')
                ->references('MaKH')
                ->on('KHACH_HANG')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('MaNV')
                ->references('MaNV')
                ->on('NHAN_VIEN')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('MaCode')
                ->references('MaCode')
                ->on('MA_GIAM_GIA')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('MaPTVC')
                ->references('MaPTVC')
                ->on('PHUONG_THUC_VAN_CHUYEN')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // 12. CHI_TIET_DON_HANG
        Schema::create('CHI_TIET_DON_HANG', function (Blueprint $table) {
            $table->string('MaDonHang', 30);
            $table->string('MaCTSP', 30);
            $table->integer('SoLuong')->default(1);
            $table->decimal('DonGia', 18, 2)->default(0);
            $table->decimal('ThanhTien', 18, 2)->default(0);
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();

            $table->primary(['MaDonHang', 'MaCTSP']);

            $table->foreign('MaDonHang')
                ->references('MaDonHang')
                ->on('DON_HANG')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('MaCTSP')
                ->references('MaCTSP')
                ->on('CHI_TIET_SP')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 13. DANH_GIA
        Schema::create('DANH_GIA', function (Blueprint $table) {
            $table->string('MaDanhGia', 30)->primary();
            $table->string('MaSP', 20);
            $table->string('MaKH', 20)->nullable();
            $table->string('TenKhachHang', 100);
            $table->unsignedTinyInteger('SoSao')->default(5);
            $table->text('NoiDung')->nullable();
            $table->boolean('TrangThai')->default(true);
            $table->timestamps();

            $table->foreign('MaSP')
                ->references('MaSP')
                ->on('SAN_PHAM')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('MaKH')
                ->references('MaKH')
                ->on('KHACH_HANG')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DANH_GIA');
        Schema::dropIfExists('CHI_TIET_DON_HANG');
        Schema::dropIfExists('DON_HANG');
        Schema::dropIfExists('GIO_HANG');
        Schema::dropIfExists('PHUONG_THUC_VAN_CHUYEN');
        Schema::dropIfExists('MA_GIAM_GIA');
        Schema::dropIfExists('NHAN_VIEN');
        Schema::dropIfExists('KHACH_HANG');
        Schema::dropIfExists('HINH_ANH_SP');
        Schema::dropIfExists('CHI_TIET_SP');
        Schema::dropIfExists('SAN_PHAM');
        Schema::dropIfExists('KICH_THUOC');
        Schema::dropIfExists('DANH_MUC');
    }
};

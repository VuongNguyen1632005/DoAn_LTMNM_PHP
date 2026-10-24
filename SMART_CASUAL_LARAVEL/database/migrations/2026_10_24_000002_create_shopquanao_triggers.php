<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Trigger BEFORE INSERT on DON_HANG to auto-populate PhiVanChuyen if 0
        DB::unprepared("
            CREATE TRIGGER trg_tudong_lay_phiship_insert
            BEFORE INSERT ON DON_HANG
            FOR EACH ROW
            BEGIN
                IF (NEW.PhiVanChuyen = 0 OR NEW.PhiVanChuyen IS NULL) AND NEW.MaPTVC IS NOT NULL THEN
                    SET NEW.PhiVanChuyen = IFNULL((SELECT GiaVanChuyen FROM PHUONG_THUC_VAN_CHUYEN WHERE MaPTVC = NEW.MaPTVC LIMIT 1), 0);
                END IF;
                SET NEW.ThanhTien = GREATEST(0, (NEW.TongTien + IFNULL(NEW.PhiVanChuyen, 0) - IFNULL(NEW.GiamGia, 0)));
            END
        ");

        // 2. Trigger AFTER INSERT on CHI_TIET_DON_HANG to update DON_HANG totals
        DB::unprepared("
            CREATE TRIGGER trg_capnhat_tong_donhang_after_insert
            AFTER INSERT ON CHI_TIET_DON_HANG
            FOR EACH ROW
            BEGIN
                UPDATE DON_HANG
                SET TongSoLuong = (SELECT IFNULL(SUM(SoLuong), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang),
                    TongTien = (SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang),
                    ThanhTien = GREATEST(0, ((SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang) + PhiVanChuyen - GiamGia))
                WHERE MaDonHang = NEW.MaDonHang;
            END
        ");

        // 3. Trigger AFTER UPDATE on CHI_TIET_DON_HANG to update DON_HANG totals
        DB::unprepared("
            CREATE TRIGGER trg_capnhat_tong_donhang_after_update
            AFTER UPDATE ON CHI_TIET_DON_HANG
            FOR EACH ROW
            BEGIN
                UPDATE DON_HANG
                SET TongSoLuong = (SELECT IFNULL(SUM(SoLuong), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang),
                    TongTien = (SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang),
                    ThanhTien = GREATEST(0, ((SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang) + PhiVanChuyen - GiamGia))
                WHERE MaDonHang = NEW.MaDonHang;
            END
        ");

        // 4. Trigger AFTER DELETE on CHI_TIET_DON_HANG to update DON_HANG totals
        DB::unprepared("
            CREATE TRIGGER trg_capnhat_tong_donhang_after_delete
            AFTER DELETE ON CHI_TIET_DON_HANG
            FOR EACH ROW
            BEGIN
                UPDATE DON_HANG
                SET TongSoLuong = (SELECT IFNULL(SUM(SoLuong), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = OLD.MaDonHang),
                    TongTien = (SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = OLD.MaDonHang),
                    ThanhTien = GREATEST(0, ((SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = OLD.MaDonHang) + PhiVanChuyen - GiamGia))
                WHERE MaDonHang = OLD.MaDonHang;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_tudong_lay_phiship_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_capnhat_tong_donhang_after_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_capnhat_tong_donhang_after_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_capnhat_tong_donhang_after_delete;");
    }
};

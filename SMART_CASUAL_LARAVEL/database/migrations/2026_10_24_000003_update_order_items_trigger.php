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
        DB::unprepared("DROP TRIGGER IF EXISTS trg_capnhat_tong_donhang_after_update;");

        DB::unprepared("
            CREATE TRIGGER trg_capnhat_tong_donhang_after_update
            AFTER UPDATE ON CHI_TIET_DON_HANG
            FOR EACH ROW
            BEGIN
                -- 1. Recalculate totals for NEW order
                UPDATE DON_HANG
                SET TongSoLuong = (SELECT IFNULL(SUM(SoLuong), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang),
                    TongTien = (SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang),
                    ThanhTien = GREATEST(0, ((SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = NEW.MaDonHang) + PhiVanChuyen - GiamGia))
                WHERE MaDonHang = NEW.MaDonHang;

                -- 2. If MaDonHang was updated/moved, recalculate totals for OLD order as well
                IF (OLD.MaDonHang <> NEW.MaDonHang) THEN
                    UPDATE DON_HANG
                    SET TongSoLuong = (SELECT IFNULL(SUM(SoLuong), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = OLD.MaDonHang),
                        TongTien = (SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = OLD.MaDonHang),
                        ThanhTien = GREATEST(0, ((SELECT IFNULL(SUM(SoLuong * DonGia), 0) FROM CHI_TIET_DON_HANG WHERE MaDonHang = OLD.MaDonHang) + PhiVanChuyen - GiamGia))
                    WHERE MaDonHang = OLD.MaDonHang;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_capnhat_tong_donhang_after_update;");

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
    }
};

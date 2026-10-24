<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonHang extends Model
{
    protected $table = 'DON_HANG';
    protected $primaryKey = 'MaDonHang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaDonHang',
        'MaKH',
        'MaNV',
        'TenNguoiNhan',
        'SoDienThoai',
        'Email',
        'MaCode',
        'MaPTVC',
        'NgayDat',
        'NgayGiaoDuKien',
        'DiaChiGiao',
        'GhiChu',
        'TongSoLuong',
        'TongTien',
        'GiamGia',
        'PhiVanChuyen',
        'ThanhTien',
        'PhuongThucThanhToan',
        'DaThanhToan',
        'TrangThaiDonHang',
        'TrangThai',
    ];

    protected $casts = [
        'NgayDat' => 'datetime',
        'NgayGiaoDuKien' => 'datetime',
        'TongSoLuong' => 'integer',
        'TongTien' => 'decimal:2',
        'GiamGia' => 'decimal:2',
        'PhiVanChuyen' => 'decimal:2',
        'ThanhTien' => 'decimal:2',
        'DaThanhToan' => 'boolean',
        'TrangThai' => 'boolean',
    ];

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }

    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'MaNV', 'MaNV');
    }

    public function maGiamGia(): BelongsTo
    {
        return $this->belongsTo(MaGiamGia::class, 'MaCode', 'MaCode');
    }

    public function phuongThucVanChuyen(): BelongsTo
    {
        return $this->belongsTo(PhuongThucVanChuyen::class, 'MaPTVC', 'MaPTVC');
    }

    public function chiTietDonHangs(): HasMany
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaDonHang', 'MaDonHang');
    }

    public function getTrangThaiTiengVietAttribute(): string
    {
        return match ($this->TrangThaiDonHang) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'packing' => 'Đang đóng gói',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao thành công',
            'cancelled' => 'Đã hủy',
            default => $this->TrangThaiDonHang,
        };
    }
}

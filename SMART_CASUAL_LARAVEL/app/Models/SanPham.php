<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SanPham extends Model
{
    protected $table = 'SAN_PHAM';
    protected $primaryKey = 'MaSP';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaSP',
        'MaDanhMuc',
        'TenSanPham',
        'MoTa',
        'TrangThai',
    ];

    protected $casts = [
        'TrangThai' => 'boolean',
    ];

    public function danhMuc(): BelongsTo
    {
        return $this->belongsTo(DanhMuc::class, 'MaDanhMuc', 'MaDanhMuc');
    }

    public function chiTietSanPhams(): HasMany
    {
        return $this->hasMany(ChiTietSanPham::class, 'MaSP', 'MaSP');
    }

    public function hinhAnhs(): HasMany
    {
        return $this->hasMany(HinhAnhSanPham::class, 'MaSP', 'MaSP');
    }

    public function anhChinh(): HasOne
    {
        return $this->hasOne(HinhAnhSanPham::class, 'MaSP', 'MaSP')
            ->where('LaAnhChinh', true);
    }

    public function danhGias(): HasMany
    {
        return $this->hasMany(DanhGia::class, 'MaSP', 'MaSP')->where('TrangThai', true);
    }

    public function getGiaHienThiAttribute(): float
    {
        $min = $this->chiTietSanPhams->where('TrangThai', true)->min('GiaBan');
        return (float) ($min ?? 0);
    }

    public function getTongTonKhoAttribute(): int
    {
        return (int) $this->chiTietSanPhams->where('TrangThai', true)->sum('SoLuong');
    }

    public function getDiemDanhGiaTrungBinhAttribute(): float
    {
        $avg = $this->danhGias->avg('SoSao');
        return round((float) ($avg ?? 5.0), 1);
    }

    public function getSoLuongDanhGiaAttribute(): int
    {
        return $this->danhGias->count();
    }
}

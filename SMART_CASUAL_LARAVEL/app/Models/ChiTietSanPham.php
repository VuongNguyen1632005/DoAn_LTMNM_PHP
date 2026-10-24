<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietSanPham extends Model
{
    protected $table = 'CHI_TIET_SP';
    protected $primaryKey = 'MaCTSP';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaCTSP',
        'MaSP',
        'MaKichThuoc',
        'GiaBan',
        'SoLuong',
        'TrangThai',
    ];

    protected $casts = [
        'GiaBan' => 'decimal:2',
        'SoLuong' => 'integer',
        'TrangThai' => 'boolean',
    ];

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }

    public function kichThuoc(): BelongsTo
    {
        return $this->belongsTo(KichThuoc::class, 'MaKichThuoc', 'MaKichThuoc');
    }
}

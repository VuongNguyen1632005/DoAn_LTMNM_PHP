<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanhGia extends Model
{
    protected $table = 'DANH_GIA';
    protected $primaryKey = 'MaDanhGia';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaDanhGia',
        'MaSP',
        'MaKH',
        'TenKhachHang',
        'SoSao',
        'NoiDung',
        'TrangThai',
    ];

    protected $casts = [
        'SoSao' => 'integer',
        'TrangThai' => 'boolean',
    ];

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }
}

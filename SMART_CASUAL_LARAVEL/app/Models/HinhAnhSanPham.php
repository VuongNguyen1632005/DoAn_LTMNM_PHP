<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HinhAnhSanPham extends Model
{
    protected $table = 'HINH_ANH_SP';
    protected $primaryKey = 'MaHinhAnh';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaHinhAnh',
        'MaSP',
        'DuongDan',
        'LaAnhChinh',
        'TrangThai',
    ];

    protected $casts = [
        'LaAnhChinh' => 'boolean',
        'TrangThai' => 'boolean',
    ];

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhuongThucVanChuyen extends Model
{
    protected $table = 'PHUONG_THUC_VAN_CHUYEN';
    protected $primaryKey = 'MaPTVC';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaPTVC',
        'TenPTVC',
        'GiaVanChuyen',
        'TrangThai',
    ];

    protected $casts = [
        'GiaVanChuyen' => 'decimal:2',
        'TrangThai' => 'boolean',
    ];
}

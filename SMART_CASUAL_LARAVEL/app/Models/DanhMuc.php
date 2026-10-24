<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMuc extends Model
{
    protected $table = 'DANH_MUC';
    protected $primaryKey = 'MaDanhMuc';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaDanhMuc',
        'TenDanhMuc',
        'TrangThai',
    ];

    protected $casts = [
        'TrangThai' => 'boolean',
    ];

    public function sanPhams(): HasMany
    {
        return $this->hasMany(SanPham::class, 'MaDanhMuc', 'MaDanhMuc');
    }
}

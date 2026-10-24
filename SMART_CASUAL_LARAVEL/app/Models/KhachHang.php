<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KhachHang extends Authenticatable
{
    use Notifiable;

    protected $table = 'KHACH_HANG';
    protected $primaryKey = 'MaKH';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaKH',
        'TenDangNhap',
        'MatKhau',
        'HoTen',
        'SoDienThoai',
        'Email',
        'DiaChi',
        'TrangThai',
    ];

    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];

    protected $casts = [
        'TrangThai' => 'boolean',
    ];

    public function getAuthPassword(): string
    {
        return $this->MatKhau;
    }

    public function getAuthIdentifierName(): string
    {
        return 'MaKH';
    }

    public function donHangs(): HasMany
    {
        return $this->hasMany(DonHang::class, 'MaKH', 'MaKH');
    }

    public function gioHangs(): HasMany
    {
        return $this->hasMany(GioHang::class, 'MaKH', 'MaKH');
    }
}

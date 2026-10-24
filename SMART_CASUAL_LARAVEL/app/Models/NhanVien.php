<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhanVien extends Authenticatable
{
    use Notifiable;

    protected $table = 'NHAN_VIEN';
    protected $primaryKey = 'MaNV';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaNV',
        'TenDangNhap',
        'MatKhau',
        'HoTen',
        'ChucVu',
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
        return 'MaNV';
    }

    public function isManager(): bool
    {
        return $this->ChucVu === 'manager';
    }

    public function isStaff(): bool
    {
        return in_array($this->ChucVu, ['manager', 'staff'], true);
    }

    public function donHangs(): HasMany
    {
        return $this->hasMany(DonHang::class, 'MaNV', 'MaNV');
    }
}

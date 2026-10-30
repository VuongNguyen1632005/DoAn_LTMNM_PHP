<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaGiamGia extends Model
{
    protected $table = 'MA_GIAM_GIA';
    protected $primaryKey = 'MaCode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaCode',
        'GiaTri',
        'LoaiGiamGia',
        'SoLuong',
        'TrangThai',
    ];

    protected $casts = [
        'GiaTri' => 'decimal:2',
        'SoLuong' => 'integer',
        'TrangThai' => 'boolean',
    ];

    public function donHangs(): HasMany
    {
        return $this->hasMany(DonHang::class, 'MaCode', 'MaCode');
    }

    public function isValid(): bool
    {
        return $this->TrangThai && $this->SoLuong > 0;
    }

    public function calculateDiscount(float $subtotal, float $shippingFee): float
    {
        if (!$this->isValid()) {
            return 0.0;
        }

        $discount = 0.0;

        if ($this->LoaiGiamGia === 'percent') {
            $discount = round($subtotal * ((float) $this->GiaTri / 100), 2);
        } elseif ($this->LoaiGiamGia === 'shipping') {
            // GiaTri <= 0 represents 100% Free Shipping (legacy FREESHIP behavior); GiaTri > 0 caps at GiaTri
            $discount = ((float) $this->GiaTri <= 0) ? $shippingFee : min($shippingFee, (float) $this->GiaTri);
        } else {
            // 'cash'
            $discount = min($subtotal, (float) $this->GiaTri);
        }

        // Invariant: discount cannot be negative and cannot exceed total bill before discount
        return max(0.0, min($discount, $subtotal + $shippingFee));
    }
}

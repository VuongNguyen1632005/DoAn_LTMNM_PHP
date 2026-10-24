<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDonHang extends Model
{
    protected $table = 'CHI_TIET_DON_HANG';
    public $incrementing = false;
    protected $primaryKey = 'MaDonHang'; // fallback

    protected $fillable = [
        'MaDonHang',
        'MaCTSP',
        'SoLuong',
        'DonGia',
        'ThanhTien',
        'TrangThai',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:2',
        'ThanhTien' => 'decimal:2',
        'TrangThai' => 'boolean',
    ];

    public function donHang(): BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'MaDonHang', 'MaDonHang');
    }

    public function chiTietSanPham(): BelongsTo
    {
        return $this->belongsTo(ChiTietSanPham::class, 'MaCTSP', 'MaCTSP');
    }

    /**
     * Set the keys for a save update/delete query (Composite Key Support).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('MaDonHang', '=', $this->getAttribute('MaDonHang') ?? ($this->original['MaDonHang'] ?? null))
              ->where('MaCTSP', '=', $this->getAttribute('MaCTSP') ?? ($this->original['MaCTSP'] ?? null));

        return $query;
    }
}

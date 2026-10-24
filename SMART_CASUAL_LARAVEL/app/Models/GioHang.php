<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GioHang extends Model
{
    protected $table = 'GIO_HANG';
    public $incrementing = false;
    protected $primaryKey = 'MaKH'; // Eloquent default fallback

    protected $fillable = [
        'MaKH',
        'MaCTSP',
        'SoLuong',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
    ];

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
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
        $query->where('MaKH', '=', $this->getAttribute('MaKH') ?? ($this->original['MaKH'] ?? null))
              ->where('MaCTSP', '=', $this->getAttribute('MaCTSP') ?? ($this->original['MaCTSP'] ?? null));

        return $query;
    }

    /**
     * Scope for composite key query
     */
    public function scopeForCustomerAndVariant($query, string $maKH, string $maCTSP)
    {
        return $query->where('MaKH', $maKH)->where('MaCTSP', $maCTSP);
    }
}

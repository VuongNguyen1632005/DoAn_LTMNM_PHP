<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutIdempotency extends Model
{
    protected $table = 'CHECKOUT_IDEMPOTENCY';

    protected $fillable = [
        'idempotency_key',
        'customer_id',
        'request_hash',
        'order_id',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'order_id', 'MaDonHang');
    }
}

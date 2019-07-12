<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NovaVoip\Traits\MaskId;

class Order extends Model
{
    use MaskId;

    const MASK_MULTIPLIER = 4013;
    const MASK_NAME = 'order_number';
    const MASK_OFFSET = 524;
    const MASK_PREFIX = 'ORD-';

    protected $appends = [self::MASK_NAME];
    protected $casts = [
        'can_be_cancelled' => 'boolean',
        'can_be_invoiced' => 'boolean',
        'cancelled_by_system' => 'boolean',
        'extra_information' => 'array',
        'is_cancelled' => 'boolean',
        'is_paid' => 'boolean',
        'needs_negotiation' => 'boolean',
        'negotiated_at',
        'price_should_be_recalculated_for_new_invoice' => 'boolean',
    ];
    protected $dates = [
        'cached_at',
        'cancelled_at',
        'invoices_issued_count',
        'negotiated_at',
        'paid_at',
    ];
    protected $fillable = [
        'cached_at',
        'can_be_cancelled',
        'can_be_invoiced',
        'cancelled_at',
        'cancelled_by',
        'cancelled_by_system',
        'cart_id',
        'country_code',
        'discount',
        'extra_information',
        'grand_total',
        'invoices_issued_count',
        'is_cancelled',
        'is_paid',
        'needs_negotiation',
        'negotiator',
        'negotiated_at',
        'paid_at',
        'price_should_be_recalculated_for_new_invoice',
        'province_code',
        'sub_total',
        'tax',
        'user_id',
    ];
    protected $table = 'orders';

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function getOrderNumberAttribute()
    {
        return self::generateMask($this->id);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function negotiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'negotiator', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use NovaVoip\Interfaces\iInvoiceProcessor;
use NovaVoip\Traits\MaskId;

class Invoice extends Model
{
    use MaskId;

    const MASK_MULTIPLIER = 2018;
    const MASK_NAME = 'invoice_number';
    const MASK_OFFSET = 1033;
    const MASK_PREFIX = 'INV-';

    const STATUS_NEW = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_PENDING_TO_BE_PROCESSED = 3;
    const STATUS_PROCESSING = 4;
    const STATUS_PROCESSED = 5;

    protected $appends = [self::MASK_NAME, 'status_caption'];
    protected $casts = ['can_be_paid_by_credit' => 'boolean', 'cancelled_by_system' => 'boolean', 'extra_information' => 'array'];
    protected $dates = ['cancelled_at', 'paid_at', 'processed_at'];
    protected $table = 'invoices';
    protected $fillable = [
        'can_be_paid_by_credit',
        'cancelled_at',
        'cancelled_by_system',
        'cancelled_by',
        'client_id',
        'country_code',
        'discount',
        'extra_information',
        'grand_total',
        'paid_at',
        'processor',
        'processed_at',
        'province_code',
        'status',
        'sub_total',
        'tag',
        'tax',
    ];

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower', 'id');
    }

    public function getInvoiceNumberAttribute()
    {
        return self::generateMask($this->id);
    }

    public function getStatusCaptionAttribute()
    {
        return self::getStatuses()[$this->status];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function originalInvoice(): BelongsTo
    {
        return $this->belongsTo(self::class, 'original_invoice', 'id');
    }

    public function payable(): bool
    {
        return is_null($this->cancelled_at) && is_null($this->paid_at);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function resolveProcessor(): iInvoiceProcessor
    {
        return new $this->processor;
    }

    public function target(): MorphTo
    {
        return $this->morphTo('target', 'target_type', 'target_id');
    }

    public function substitutedInvoice(): BelongsTo
    {
        return $this->belongsTo(self::class, 'substituted', 'id');
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => __('Pending Payment'),
            self::STATUS_CANCELLED => __('Cancelled'),
            self::STATUS_PENDING_TO_BE_PROCESSED => __('Pending'),
            self::STATUS_PROCESSING => __('Processing'),
            self::STATUS_PROCESSED => __('Processed'),
        ];
    }
}

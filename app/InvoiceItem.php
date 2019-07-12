<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceItem extends Model
{
    protected $casts = [
        'extra_information' => 'array',
        'include_in_calculations' => 'boolean',
        'hide_from_client' => 'boolean',
    ];
    protected $dates = ['cached_at'];
    protected $fillable = [
        'amount',
        'cost',
        'description',
        'discount',
        'extra_information',
        'grand_total',
        'hide_from_client',
        'include_in_calculations',
        'price',
        'product_type_id',
        'sub_total',
        'tag',
        'tax',
    ];
    protected $table = 'invoice_items';

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function parentItem(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }
}

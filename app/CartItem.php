<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    public $casts = ['extra_information' => 'array'];
    protected $fillable = ['amount', 'extra_information'];
    protected $table = 'cart_items';

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
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

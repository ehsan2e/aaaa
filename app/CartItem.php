<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    public $casts = ['can_be_edited' => 'boolean', 'can_be_removed' => 'boolean', 'extra_information' => 'array', 'include_in_calculations' => 'boolean'];
    protected $dates = ['cached_at'];
    protected $fillable = ['amount', 'cached_at', 'can_be_edited', 'can_be_removed', 'discount', 'extra_information', 'grand_total', 'include_in_calculations', 'sub_total', 'tax'];
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

    /**
     * @param ProductType $productType
     * @param int $amount
     * @param CartItem|null $parent
     * @param array $extraInformation
     * @return CartItem
     */
    public static function generateItem(ProductType $productType, int $amount = 1, CartItem $parent = null, array $extraInformation = []): CartItem
    {
        $item = new self(['amount' => $amount, 'extra_information' => $extraInformation]);
        $item->productType()->associate($productType);
        if(isset($parent)){
            $item->parentItem()->associate($parent);
        }
        return $item;
    }
}

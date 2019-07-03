<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $casts = ['active' =>'boolean', 'extra_information' => 'array'];
    protected $fillable = ['active', 'extra_information', 'session'];
    protected $table = 'carts';

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}

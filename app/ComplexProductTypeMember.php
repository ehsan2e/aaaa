<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ComplexProductTypeMember extends Pivot
{
    protected $casts = ['settings' => 'array'];
    protected $fillable = ['settings'];
    protected $table = 'complex_product_type_members';

    public function complexProductType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'complex_product_type_id', 'id');
    }

    public function simpleProductType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'simple_product_type_id', 'id');
    }
}

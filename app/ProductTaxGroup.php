<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductTypeTaxGroup extends Pivot
{
    protected $table = 'product_type_tax_groups';

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }

    public function taxGroup(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class, 'tax_group_id', 'id');
    }
}

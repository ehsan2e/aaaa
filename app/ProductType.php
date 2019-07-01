<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use NovaVoip\Traits\FieldLevelAccessControl;

class ProductType extends Model
{
    use FieldLevelAccessControl;

    protected $appends = ['price'];
    protected $casts = [
        'active' => 'boolean',
        'on_sale' => 'boolean',
        'stock_less' => 'boolean',
        'allow_back_order' => 'boolean',
        'show_out_of_stock' => 'boolean',
        'in_promotion' => 'boolean',
        'custom_attributes' => 'array',
    ];
    protected $dates = ['promotion_starts_at', 'promotion_ends_at'];
    /**
     * @return array
     */
    protected function modelRelations(): array
    {
        return [
            'category' => ['category_id', false],
            'supplier' => ['supplier_id', false],
        ];
    }
    protected $fillable = [
        'name', 'description', 'picture', 'cost', 'original_price', 'special_price', 'supplier_sku', 'supplier_share', 'promotion_price', 'promotion_starts_at', 'promotion_ends_at', 'custom_attributes',
        'active','on_sale','stock_less','allow_back_order','show_out_of_stock','in_promotion'
    ];

    protected $table = 'product_types';

    public function getPriceAttribute()
    {
        if ($this->promotion_price && $this->isOnPromotion()) {
            return $this->promotion_price;
        }
        return $this->special_price ?? $this->original_price;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by', 'id');
    }

    public function isOnPromotion(): bool
    {
        if ($this->in_promotion) {
            return true;
        }
        $now = Carbon::now();
        if (
            isset($this->promotion_starts_at)
            && $now->isAfter($this->promotion_starts_at)
            && (
                (!isset($this->promotion_ends_at))
                || $now->isAfter($this->promotion_ends_at)
            )
        ) {
            return true;
        }
        if (
            isset($this->promotion_ends_at)
            && $now->isBefore($this->promotion_ends_at)
            && (
                (!isset($this->promotion_starts_at))
                || $now->isBefore($this->promotion_starts_at)
            )
        ) {
            return true;
        }

        return false;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * @param User $creator
     * @param array $data
     * @return ProductType|null
     */
    public static function createNewProductType(User $creator, array $data): ?ProductType
    {
        $instance = new self($data);
        $instance->sku = $data['sku'];
        $booleanFields = $instance->getBooleanFields();
        array_walk($booleanFields, function ($name) use ($data, $instance) {
            $instance->{$name} = isset($data[$name]);
        });
        $instance->category_id = $data['category_id'] ?? null;
        $instance->supplier_id = $data['supplier_id'] ?? null;
        $instance->creator()->associate($creator);
        return $instance->save() ? $instance : null;
    }
}

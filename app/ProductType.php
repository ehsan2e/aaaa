<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use NovaVoip\Traits\FieldLevelAccessControl;

class ProductType extends Model
{
    use FieldLevelAccessControl;

    const PERIODICITY_DAILY = 1;
    const PERIODICITY_WEEKLY = 2;
    const PERIODICITY_MONTHLY = 3;
    const PERIODICITY_QUARTERLY = 4;
    const PERIODICITY_HALF_YEARLY = 5;
    const PERIODICITY_YEARLY = 6;
    const PERIODICITY_LIFETIME = 7;
    const PERIODS = [
        self::PERIODICITY_DAILY,
        self::PERIODICITY_WEEKLY,
        self::PERIODICITY_MONTHLY,
        self::PERIODICITY_QUARTERLY,
        self::PERIODICITY_HALF_YEARLY,
        self::PERIODICITY_YEARLY,
        self::PERIODICITY_LIFETIME,
    ];

    protected $appends = ['price'];
    protected $casts = [
        'active' => 'boolean',
        'on_sale' => 'boolean',
        'stock_less' => 'boolean',
        'allow_back_order' => 'boolean',
        'show_out_of_stock' => 'boolean',
        'in_promotion' => 'boolean',
        'imposes_pre_invoice_negotiation' => 'boolean',
        'custom_attributes' => 'array',
        'upsell_alternatives' => 'array',
    ];
    protected $dates = ['promotion_starts_at', 'promotion_ends_at'];

    /**
     * @return array
     */
    protected function modelRelations(): array
    {
        return [
            'category' => ['category_id', false, BelongsTo::class],
            'supplier' => ['supplier_id', false, BelongsTo::class],
            'taxGroups' => ['tax_groups', false, BelongsToMany::class],
        ];
    }

    protected $fillable = [
        'name', 'description', 'picture', 'cost', 'original_price', 'special_price', 'supplier_sku', 'supplier_share', 'promotion_price', 'promotion_starts_at', 'promotion_ends_at', 'custom_attributes',
        'active', 'on_sale', 'stock_less', 'allow_back_order', 'show_out_of_stock', 'in_promotion', 'imposes_pre_invoice_negotiation', 'periodicity', 'upsell_alternatives'
    ];

    protected $table = 'product_types';

    public function getPriceAttribute()
    {
        return self::calculatePrice($this);
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

    public function taxGroups(): BelongsToMany
    {
        return $this->belongsToMany(TaxGroup::class, 'product_type_tax_groups', 'product_type_id', 'tax_group_id', 'id', 'id');
    }


    public static function calculatePrice(Model $productType)
    {
        if ($productType->promotion_price && $productType->isOnPromotion()) {
            return $productType->promotion_price;
        }
        return $productType->special_price ?? $productType->original_price;
    }

    /**
     * @param User $creator
     * @param array $data
     * @return ProductType|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createNewProductType(User $creator, array $data): ?ProductType
    {
        return supervisedTransaction(function () use ($creator, $data): ?ProductType {
            $instance = new self($data);
            $instance->sku = $data['sku'];
            $booleanFields = $instance->getBooleanFields();
            array_walk($booleanFields, function ($name) use ($data, $instance) {
                $instance->{$name} = isset($data[$name]);
            });
            $instance->category_id = $data['category_id'] ?? null;
            $instance->supplier_id = $data['supplier_id'] ?? null;
            $instance->creator()->associate($creator);
            if (!$instance->save()) {
                return null;
            }
            if (isset($data['tax_groups'])) {
                $instance->taxGroups()->sync($data['tax_groups']);
            }
            return $instance;
        }, null, false, false);
    }

    public static function getPeriods(): array
    {
        return [
            self::PERIODICITY_DAILY => __('Daily'),
            self::PERIODICITY_WEEKLY => __('Weekly'),
            self::PERIODICITY_MONTHLY => __('Monthly'),
            self::PERIODICITY_QUARTERLY => __('Quarterly'),
            self::PERIODICITY_HALF_YEARLY => __('Half yearly'),
            self::PERIODICITY_YEARLY => __('Yearly'),
            self::PERIODICITY_LIFETIME => __('Lifetime'),
        ];
    }
}

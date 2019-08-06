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

    const TYPE_SIMPLE = 1;
    const TYPE_CONFIGURABLE = 2;
    const TYPES = [
        self::TYPE_SIMPLE,
        self::TYPE_CONFIGURABLE,
    ];


    protected $appends = ['price', 'type_caption'];
    protected $casts = [
        'active' => 'boolean',
        'on_sale' => 'boolean',
        'appears_in_listing' => 'boolean',
        'stock_less' => 'boolean',
        'allow_back_order' => 'boolean',
        'show_out_of_stock' => 'boolean',
        'in_promotion' => 'boolean',
        'imposes_pre_invoice_negotiation' => 'boolean',
        'custom_attributes' => 'array',
        'upsell_alternatives' => 'array',
        'complex_settings' => 'array',
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
        'active', 'on_sale', 'appears_in_listing', 'stock_less', 'allow_back_order', 'show_out_of_stock', 'in_promotion', 'imposes_pre_invoice_negotiation', 'periodicity', 'upsell_alternatives', 'complex_settings'
    ];

    protected $table = 'product_types';

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function complexProducts(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'complex_product_type_members', 'simple_product_type_id', 'complex_product_type_id', 'id', 'id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by', 'id');
    }

    public function getPriceAttribute()
    {
        return self::calculatePrice($this);
    }

    public function getTypeCaptionAttribute()
    {
        return self::getTypes()[$this->type] ?? __('Unknown (:type)', ['type' => $this->type]);
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

    public function simpleProducts(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'complex_product_type_members', 'complex_product_type_id', 'simple_product_type_id', 'id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function taxGroups(): BelongsToMany
    {
        return $this->belongsToMany(TaxGroup::class, 'product_type_tax_groups', 'product_type_id', 'tax_group_id', 'id', 'id');
    }

    /**
     * @param User $editor
     * @param array $data
     * @return bool
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public function updateInfo(User $editor, array $data): bool
    {
        return supervisedTransaction(function() use ($editor, $data) {
            $instance = $this;
            $instance->fill($data);
            $instance->prepareBooleanFields($data);
            $instance->editor()->associate($editor);
            if (!$instance->save()) {
                return false;
            }
            $instance->simpleProducts()->sync($data['simple_products']);
            return true;
        }, false, false, false);
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
    public static function createNewConfigurableProductType(User $creator, array $data): ?ProductType
    {
        return supervisedTransaction(function () use ($creator, $data): ?ProductType {
            $instance = new self($data);
            $instance->type = self::TYPE_CONFIGURABLE;
            $instance->prepareBooleanFields($data);
            $instance->category_id = $data['category_id'] ?? null;
            $instance->creator()->associate($creator);
            if (!$instance->save()) {
                return null;
            }
            $instance->simpleProducts()->sync($data['simple_products']);
            return $instance;
        }, null, false, false);
    }

    /**
     * @param User $creator
     * @param array $data
     * @return ProductType|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createNewSimpleProductType(User $creator, array $data): ?ProductType
    {
        return supervisedTransaction(function () use ($creator, $data): ?ProductType {
            $instance = new self($data);
            $instance->type = self::TYPE_SIMPLE;
            $instance->sku = $data['sku'];
            $instance->prepareBooleanFields($data);
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

    public static function getTypes(): array
    {
        return [
            self::TYPE_SIMPLE => __('Simple'),
            self::TYPE_CONFIGURABLE => __('Configurable'),
        ];
    }

    public static function getTypeSlugs(): array
    {
        return [
            self::TYPE_SIMPLE => 'simple',
            self::TYPE_CONFIGURABLE => 'configurable',
        ];
    }
}

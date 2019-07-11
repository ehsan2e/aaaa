<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $casts = [
        'extra_information' => 'array',
        'include_in_calculations' => 'boolean',
    ];
    protected $dates = ['cached_at'];
    protected $fillable = [
        'amount',
        'cached_at',
        'discount',
        'extra_information',
        'grand_total',
        'include_in_calculations',
        'order_id',
        'product_type_id',
        'sub_total',
        'tax',
    ];
    protected $table = 'order_items';

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
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
     * @param callable|null $fn
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function updateFigures(callable $fn = null): bool
    {
        return supervisedTransaction(function () use ($fn): bool {
            $cart = $this;
            if ($fn && (!$fn($cart))) {
                return false;
            }
            $countryCode = $cart->country_code;
            $provinceCode = $cart->province_code;
            //todo: implement discount percentage
            $discountPercentage = 0;
            /** @var Collection $cartItems */
            $cartItems = $this->items()->with([
                'productType.taxGroups',
                'productType.taxGroups.rules' => function (HasMany $query) use ($countryCode, $provinceCode) {
                    if ($countryCode) {
                        $query->where(function (Builder $q) use ($countryCode, $provinceCode) {
                            $q->whereNull('country_code')->orWhere('country_code', $countryCode);
                        });
                    } elseif (!$provinceCode) {
                        $query->whereNull('country_code');
                    }

                    if ($provinceCode) {
                        $query->where(function (Builder $q) use ($countryCode, $provinceCode) {
                            $q->whereNull('province_code')->orWhere('province_code', $provinceCode);
                        });
                    } else {
                        $query->whereNull('province_code');
                    }
                }])->get();
            $now = Carbon::now();

            $cart->cached_at = $now;
            $cart->discount = 0;
            $cart->grand_total = 0;
            $cart->sub_total = 0;
            $cart->tax = 0;

            /** @var CartItem $cartItem */
            foreach ($cartItems as $cartItem) {
                if (!$cartItem->include_in_calculations) {
                    continue;
                }
                $cartItem->cached_at = $now;
                $cartItem->sub_total = $cartItem->productType->price * $cartItem->amount;
                $cartItem->discount = $cartItem->sub_total * $discountPercentage;
                $beforeTax = $cartItem->sub_total - $cartItem->discount;
                $cartItem->tax = 0;
                /** @var TaxGroup $taxGroup */
                foreach ($cartItem->productType->taxGroups as $taxGroup) {
                    if (!$taxGroup->active) {
                        continue;
                    }
                    if (count($taxGroup->rules) === 0) {
                        $cartItem->tax += $taxGroup->calculate($beforeTax);
                        continue;
                    }

                    /** @var TaxRule $taxGroupRule */
                    $taxRule = $taxGroup->rules[0];
                    $taxRulePriority = $taxRule->priority;
                    /** @var TaxRule $rule */
                    foreach ($taxGroup->rules as $rule) {
                        if ($rule->priority > $taxRulePriority) {
                            $taxRule = $rule;
                            $rule->priority = $taxRulePriority;
                        }
                    }

                    $cartItem->tax += $rule->calculate($beforeTax);
                }

                $cartItem->grand_total = $beforeTax + $cartItem->tax;
                $cartItem->save();

                $cart->discount += $cartItem->discount;
                $cart->grand_total += $cartItem->grand_total;
                $cart->sub_total += $cartItem->sub_total;
                $cart->tax += $cartItem->tax;
            }
            $cart->save();
            return true;
        }, false, true, false);
    }

    /**
     * @param ProductType $productType
     * @param int $amount
     * @param OrderItem $parent
     * @param array $extraInformation
     * @return OrderItem
     */
    public static function generateItem(ProductType $productType, int $amount = 1, OrderItem $parent = null, array $extraInformation = []): OrderItem
    {
        $item = new self(['amount' => $amount, 'extra_information' => $extraInformation]);
        $item->productType()->associate($productType);
        if (isset($parent)) {
            $item->parentItem()->associate($parent);
        }
        return $item;
    }
}
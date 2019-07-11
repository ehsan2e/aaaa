<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;

/**
 * Class Cart
 * @method static Builder openForModification()
 * @package App
 */
class Cart extends Model
{
    protected $casts = ['active' => 'boolean', 'extra_information' => 'array'];
    protected $dates = ['cached_at'];
    protected $fillable = ['active', 'cached_at', 'discount', 'extra_information', 'grand_total', 'session', 'sub_total', 'tax'];
    protected $table = 'carts';

    /**
     * @param bool $createInvoice
     * @param bool $force
     * @param null $insight
     * @return Order|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public function createOrder(bool $createInvoice = true, bool $force = false, &$insight = null): ?Order
    {
        return supervisedTransaction(function ($insight) use ($createInvoice, $force): ?Order {
            /** @var Cart $lockedCart */
            $lockedCart = self::lockForUpdate()->find($this->id);
            if (!($lockedCart && ($lockedCart->active || $force))) {
                $insight->message = __('Cart is closed');
                return null;
            }
            $lockedCart->active = false;
            $lockedCart->save();

            $lockedCart->load(['items.productType', 'user']);
            /** @var Order $order */
            $order = New Order($lockedCart->attributesToArray());
            $order->cart()->associate($lockedCart);

            $orderItems = [];
            $hasSpecialItems = false;
            /** @var CartItem $item */
            foreach ($lockedCart->items as $item) {
                $orderItem = new OrderItem($item->attributesToArray());
                $orderItems[$item->id] = [
                    'parent' => $item->parent_id,
                    'orderItem' => $orderItem,
                ];
                $hasSpecialItems = $hasSpecialItems || $item->productType->imposes_pre_invoice_negotiation;
            }

            $order->can_be_invoiced = $hasSpecialItems;
            $order->needs_negotiation = $hasSpecialItems;
            $order->save();

            do {
                $readyItems = array_filter($orderItems, function ($item) use ($orderItems) {
                    return (!$item['orderItem']->id) && (is_null($item['parent']) || isset($orderItems[$item['parent']]['orderItem']->id));
                });
                $c = count($readyItems);
                if ($c > 0) {
                    $savableOrderItems = array_map(function ($item) use ($orderItems) {
                        $orderItem = $item['orderItem'];
                        $orderItem->parent_id = isset($item['parent']) ? $orderItems[$item['parent']]['orderItem']->id : null;
                        return $orderItem;
                    }, $readyItems);
                    $order->items()->saveMany($savableOrderItems);
                }
            } while ($c > 0);

            if(!$hasSpecialItems){
                $order->issueInvoice();
            }
            return $order;
        }, null, true, false, $insight);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    /**
     * @param CartItem $cartItem
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function removeItem(CartItem $cartItem): bool
    {
        return $this->updateFigures(function () use ($cartItem) {
            $cartItem->delete();
            return true;
        });
    }

    /**
     * @param Builder $query
     */
    public function scopeOpenForModification(Builder $query)
    {
        $query->where('active', true);
    }

    /**
     * @param string $countryCode
     * @param string $provinceCode
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function updateTaxRegion(?string $countryCode, ?string $provinceCode): bool
    {
        return $this->updateFigures(function (Cart $cart) use ($countryCode, $provinceCode) {
            $cart->country_code = $countryCode;
            $cart->province_code = $provinceCode;
            return true;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param User|null $user
     * @param string|null $sessionId
     * @return Cart
     */
    public static function loadCart(?User $user, string $sessionId = null): Cart
    {
        if (!(isset($user) || isset($sessionId))) {
            throw new \InvalidArgumentException('At least user or sessionId should be provided');
        }
        $cart = isset($user) ?
            self::openForModification()->where('user_id', $user->id)->first() :
            (
            isset($sessionId) ?
                self::openForModification()->where('session', $sessionId)->first() :
                null
            );
        if (isset($cart)) {
            return $cart;
        }

        $cart = new self(['session' => $sessionId]);
        if (isset($user)) {
            if ($user->carts()->save($cart) === false) {
                throw new \RuntimeException('Could not create new cart for user id');
            }
            return $cart;
        }

        if (isset($sessionId)) {
            if (!$cart->save()) {
                throw new \RuntimeException('Could not create new cart for session');
            }
            return $cart;
        }
    }

    /**
     * @param int $id
     * @param User|null $user
     * @param string|null $sessionId
     * @return Cart
     */
    public static function loadCartById(?int $id, ?User $user, string $sessionId = null): Cart
    {
        if (!isset($id)) {
            return self::loadCart($user, $sessionId);
        }

        /** @var Cart|null $cart */
        $cart = self::openForModification()->find($id);
        return $cart ?? self::loadCart($user, $sessionId);
    }

    /**
     * @param User $user
     * @param int $cartId
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public static function mergeUserCart(User $user, int $cartId)
    {
        supervisedTransaction(function () use ($user, $cartId) {
            $carts = self::openForModification()->lockForUpdate()->where(function (Builder $query) use ($user, $cartId) {
                $query->where('user_id', $user->id)
                    ->orWhere('id', $cartId);
            })->orderBy('id', 'desc')->get();
            $cartsCount = count($carts);
            if ($cartsCount === 0) {
                return;
            }
            if ($cartsCount > 1) {
                $cartIds = $carts->pluck('id')->toArray();
                rsort($cartIds);
                $firstId = array_shift($cartIds);
                CartItem::whereIn('cart_id', $cartIds)->update(['cart_id' => $firstId]);
                Cart::whereIn('id', $cartIds)->delete();
            }

            /** @var Cart $cart */
            $cart = $carts[0];
            $cart->user_id = $user->id;
            $cart->session = null;
            $cart->save();
            if (($cartsCount > 1) && !$cart->updateFigures()) {
                throw new SupervisedTransactionException('Could not update the merged cart');
            }
        }, null, false, false);
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
}

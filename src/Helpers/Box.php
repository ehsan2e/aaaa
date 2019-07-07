<?php

namespace NovaVoip\Helpers;


use App\Cart;
use App\CartItem;
use App\ProductCategory;
use App\ProductType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use function NovaVoip\supervisedTransaction;

class Box
{
    protected static $boxes;

    /**
     * @param Cart $cart
     * @param int $employeeNumber
     * @param string $adminPassword
     * @param string $domain
     * @param array $boxServices
     * @param CartItem|null $originalItem
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public static function addToCart(Cart $cart, int $employeeNumber, string $adminPassword, string $domain, array $boxServices=[], CartItem $originalItem=null): bool
    {
        return supervisedTransaction(function () use ($cart, $employeeNumber, $adminPassword, $domain, $boxServices, $originalItem): bool{
            if($originalItem){
                $originalItem->delete();
            }
            /** @var Cart $lockedCart */
            $lockedCart = Cart::openForModification()->lockForUpdate()->find($cart->id);
            if(!$lockedCart){
                return false;
            }

            $box = self::resolveBox($employeeNumber);
            if(!$box){
                return false;
            }

            $boxCartItem = new CartItem([
                'amount' => 1,
                'can_be_edited' => true,
                'can_be_removed' => true,
                'extra_information' => [
                    'admin_password' => $adminPassword,
                    'domain' => $domain,
                    'employee_number' => $employeeNumber
                ],
                'include_in_calculations' => true
            ]);
            $boxCartItem->productType()->associate($box);
            if(!$lockedCart->items()->save($boxCartItem)){
                return false;
            }
            $services = BoxService::load();
            $serviceCartItems = [];
            /** @var ProductType $service */
            foreach ($services as $service){
                if($service->custom_attributes['mandatory'] || in_array($service->id, $boxServices)){
                    $serviceCartItem = new CartItem([
                        'amount' => 1,
                        'can_be_edited' => false,
                        'can_be_removed' => !$service->custom_attributes['mandatory'],
                        'include_in_calculations' => true
                    ]);
                    $serviceCartItem->productType()->associate($service);
                    $serviceCartItem->parentItem()->associate($boxCartItem);
                    $serviceCartItems[] = $serviceCartItem;
                }
            }
            $cart->items()->saveMany($serviceCartItems);
            return $cart->updateFigures();
        }, false, false, false);
    }

    /**
     * @return array
     */
    public static function flattenedBoxes(): array
    {
        if (!self::$boxes) {
            $boxes = self::load()->map(function (ProductType $box) {
                return [
                    'id' => $box->id,
                    'max_employee' => $box->custom_attributes['max_employee'] ?? null,
                    'min_employee' => $box->custom_attributes['min_employee'] ?? null,
                    'name' => $box->name,
                    'price' => $box->price,
                    'sku' => $box->sku,
                ];
            })->toArray();
            usort($boxes, function ($a, $b) {
                if (is_null($a['min_employee'])) {
                    return is_null($b['min_employee']) ? 0 : -1;
                }
                if (is_null($b['min_employee'])) {
                    return 1;
                }
                return $a['min_employee'] <=> $b['min_employee'];
            });
            self::$boxes = $boxes;
        }
        return self::$boxes;
    }

    /**
     * @param bool $onlyActives
     * @return Collection
     */
    public static function load(bool $onlyActives = true): Collection
    {
        /** @var Builder $categorySubQuery */
        $categorySubQuery = ProductCategory::query()->select('id')->where('code', config('nova.box_category_code'));
        /** @var Builder $query */
        $query = ProductType::query()->whereIn('category_id', $categorySubQuery);
        if ($onlyActives) {
            $query->where('active', true);

        }
        return $query->get();
    }

    /**
     * @param int $employeeNumber
     * @return ProductType|null
     */
    public static function resolveBox(int $employeeNumber): ?ProductType
    {
        foreach (self::flattenedBoxes() as $b){
            if(is_null($b['max_employee']) || ($employeeNumber <= $b['max_employee'])){
                return ProductType::find($b['id']);
            }
        }
        return null;
    }
}
<?php

namespace NovaVoip\Helpers;


use App\ProductCategory;
use App\ProductType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BoxService
{
    /**
     * @param bool $onlyActives
     * @param array|null $types
     * @param callable|null $fn
     * @return Collection
     */
    public static function load(bool $onlyActives = true, array $types = null, callable $fn = null): Collection
    {
        /** @var Builder $categorySubQuery */
        $categorySubQuery = ProductCategory::query()->select('id')
            ->where('code', config('nova.box_service_category_code'))
            ->where('appears_in_listing', true);
        /** @var Builder $query */
        $query = ProductType::query()->whereIn('category_id', $categorySubQuery);
        if ($onlyActives) {
            $query->where('active', true);
        }
        if(isset($types)){
            $query->whereIn('type', $types);
        }
        if(isset($fn)){
            $fn($query);
        }
        return $query->get();
    }
}
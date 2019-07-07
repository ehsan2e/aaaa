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
     * @return Collection
     */
    public static function load(bool $onlyActives = true): Collection
    {
        /** @var Builder $categorySubQuery */
        $categorySubQuery = ProductCategory::query()->select('id')->where('code', config('nova.box_service_category_code'));
        /** @var Builder $query */
        $query = ProductType::query()->whereIn('category_id', $categorySubQuery);
        if ($onlyActives) {
            $query->where('active', true);

        }
        return $query->get();
    }
}
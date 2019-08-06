<?php

namespace App\Rules;

use App\ProductType;
use Illuminate\Contracts\Validation\Rule;

class ConfigurableProductSimpleProducts implements Rule
{
    protected $categoryId;
    protected $defaultProduct;
    protected $errorMessage = 'Unknown';

    /**
     * Create a new rule instance.
     *
     * @param $categoryId
     * @param $defaultProduct
     */
    public function __construct($categoryId, $defaultProduct)
    {
        $this->categoryId = $categoryId;
        $this->defaultProduct = $defaultProduct;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //        dd($attribute, $value, $this->categoryId, $this->defaultProduct);
        if($this->defaultProduct && !in_array($this->defaultProduct, $value)){
            $this->errorMessage = trans('validation.configurable_product_default_simple_products');
            return false;
        }
        $value = array_unique($value);

        $this->errorMessage = trans('validation.configurable_product_simple_products');
        $productTypeQuery = ProductType::query()->whereIn('id', $value);
        if(isset($this->categoryId)){
            $productTypeQuery->where('category_id', $this->categoryId);
        }else{
            $productTypeQuery->whereNull('category_id');
        }
        return count($value) === $productTypeQuery->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}

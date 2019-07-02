<?php

namespace App\Http\Controllers\Dashboard\Admin\Catalog;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\ProductCategory;
use App\Rules\AttributeSet;
use App\Rules\AttributeSetLookupValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use function NovaVoip\sortedLanguages;

class ProductCategoryController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'productCategories';

    /**
     * @var
     */
    protected $searchableFields = ['product_categories.code', 'product_categories.name', 'product_categories.address', 'pc2.name'];

    /**
     * @var string
     */
    protected $viewBasePath = 'catalog.product-category';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return ProductCategory::query()
            ->leftJoin('product_categories AS pc2', 'pc2.id', '=', 'product_categories.parent_id')
            ->select(['product_categories.id','product_categories.name','product_categories.code','product_categories.parent_id','product_categories.active','product_categories.address', 'pc2.name as parent_name']);
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [])
    {
        $languages = array_map(function ($item) {
            return $item['display'];
        }, sortedLanguages());
        $productCategories = ProductCategory::orderBy('name')->get();
        return parent::renderForm($view, compact('languages', 'productCategories') + $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'parent_id' => [],
            'code' => [Rule::unique('product_categories')->whereNotNull('code')],
            'name' => ['required'],
            'active' => ['boolean'],
            'custom_attributes' => ['array', new AttributeSet()],
            'custom_attributes.*' => [],
            'custom_attributes.*.name' => ['required'],
            'custom_attributes.*.caption' => ['required'],
            'custom_attributes.*.required' => ['boolean'],
            'custom_attributes.*.type' => ['required', Rule::in(array_keys(ProductCategory::getAttributeTypes()))],
            'custom_attributes.*.lookupValues' => ['required_if:custom_attributes.*.type,lookup', 'array', 'min:1', new AttributeSetLookupValue($data)],
            'custom_attributes.*.lookupValues.*.caption' => ['required'],
            'custom_attributes.*.lookupValues.*.value' => ['required'],
            ]);
        if (ProductCategory::createNewCategory($data, $insight)) {
            flash()->success($insight->message ?? __('Product category :name was created successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.catalog.product-category.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategory $productCategory)
    {
        return $this->renderForm('dashboard.admin.catalog.product-category.edit', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->all();
        $request->validate([
            'parent_id' => [],
            'code' => [Rule::unique('product_categories')->whereNotNull('code')->ignore($productCategory->id)],
            'name' => ['required'],
            'active' => ['boolean'],
            'custom_attributes' => ['array', new AttributeSet()],
            'custom_attributes.*' => [],
            'custom_attributes.*.name' => ['required'],
            'custom_attributes.*.caption' => ['required'],
            'custom_attributes.*.required' => ['boolean'],
            'custom_attributes.*.type' => ['required', Rule::in(array_keys(ProductCategory::getAttributeTypes()))],
            'custom_attributes.*.lookupValues' => ['required_if:custom_attributes.*.type,lookup', 'array', 'min:1', new AttributeSetLookupValue($data)],
            'custom_attributes.*.lookupValues.*.caption' => ['required'],
            'custom_attributes.*.lookupValues.*.value' => ['required'],
        ]);
        if ($productCategory->updateInfo($data, $insight)) {
            flash()->success($insight->message ?? __('Product category :name was updated successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.catalog.product-category.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }
}

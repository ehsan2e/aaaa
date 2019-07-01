<?php

namespace App\Http\Controllers\Dashboard\Admin\Catalog;

use App\Http\Controllers\Dashboard\Admin\AbstarctAdminController;
use App\ProductCategory;
use App\ProductType;
use App\Rules\ExistingModel;
use App\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use NovaVoip\Interfaces\iPaginationGenerator;

class ProductTypeController extends AbstarctAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'productTypes';

    /**
     * @var array
     */
    protected $searchableFields = ['product_types.sku', 'product_types.name', 'product_types.supplier_sku', 'product_categories.name', 'suppliers.name'];

    /**
     * @var string
     */
    protected $viewBasePath = 'catalog.product-type';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return ProductType::query()
            ->leftJoin('product_categories', 'product_categories.id', '=', 'product_types.category_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'product_types.supplier_id')
            ->leftJoin('users AS c', 'c.id', '=', 'product_types.created_by')
            ->leftJoin('users AS e', 'e.id', '=', 'product_types.edited_by')
            ->select(['product_types.*', 'product_categories.code AS category_code', 'product_categories.name AS category_name', 'c.name as creator_name', 'e.name as editor_name', 'suppliers.name as supplier_name']);
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [])
    {
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();
        return parent::renderForm($view, compact('suppliers') + $data);
    }

    protected function prePaginationRender(iPaginationGenerator $paginationGenerator): iPaginationGenerator
    {
        $paginationGenerator->bindQueryParamFilter('category_code', 'product_categories.code');
        return parent::prePaginationRender($paginationGenerator);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \ErrorException
     */
    public function create()
    {
        $categoryId = \Illuminate\Support\Facades\Request::query('category');
        if ((!$categoryId) && \Illuminate\Support\Facades\Request::has('category')) {
            return parent::create();
        }
        if ((!$categoryId) || (!($productCategory = ProductCategory::find($categoryId)))) {
            $productCategories = ProductCategory::select('id', 'name')->get();
            return view('dashboard.admin.catalog.product-type.category-selection', compact('productCategories'));
        }
        return $this->renderForm(sprintf($this->viewPath ?? 'dashboard.admin.%s.create', $this->getViewBasePath()), compact('productCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $v = Validator::make($data, []);
        $rules = [
            'category_id' => ['nullable', (new ExistingModel('product_categories'))->setMessage(__('Select a valid category'))],
            'sku' => ['required', Rule::unique('product_types')],
            'name' => ['required'],
            'description' => [],
            'picture' => [],
            'active' => ['boolean'],
            'on_sale' => ['boolean'],
            'supplier_id' => ['nullable', (new ExistingModel('suppliers'))->setMessage(__('Select a valid supplier'))],
            'supplier_sku' => [],
            'supplier_share' => ['nullable', 'numeric'],
            'original_price' => ['required', 'numeric'],
            'special_price' => ['nullable', 'numeric'],
            'cost' => ['nullable', 'numeric'],
            'stock_less' => ['boolean'],
            'allow_back_order' => ['boolean'],
            'show_out_of_stock' => ['boolean'],
            'promotion_price' => ['required_with:in_promotion,promotion_starts_at,promotion_ends_at', 'nullable', 'numeric'],
            'in_promotion' => ['boolean'],
            'promotion_starts_at' => ['nullable', 'date'],
            'promotion_ends_at' => ['nullable', 'date'],
        ];
        if(isset($data['category_id'])){
            /** @var ProductCategory $productCategory */
           $productCategory = ProductCategory::find($data['category_id']);
           $rules += $productCategory->getCustomAttributeValidationRules();
            $v->setAttributeNames($productCategory->getCustomAttributeCaptions(true));
        }
        $v->setRules($rules);
        if($v->fails()){
            return back()->withInput()->withErrors($v);
        }
        if (ProductType::createNewProductType(Auth::user(), $data)) {
            flash()->success(__('Product type :name was created successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.catalog.product-type.index');
        }

        flash()->error(__('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductType $productType
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductType $productType)
    {
        $productCategory = $productType->category;
        return $this->renderForm('dashboard.admin.catalog.product-type.edit', compact('productType', 'productCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ProductType $productType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductType $productType)
    {
        $partialData = $productType->preparePartialData(Auth::user(), $request->except(['_method', '_token']));
        $rules = [
            'category_id' => ['nullable', (new ExistingModel('product_categories'))->setMessage(__('Select a valid category'))],
            'name' => ['required'],
            'description' => [],
            'picture' => [],
            'active' => ['boolean'],
            'on_sale' => ['boolean'],
            'supplier_id' => ['nullable', (new ExistingModel('suppliers'))->setMessage(__('Select a valid supplier'))],
            'supplier_sku' => [],
            'supplier_share' => ['nullable', 'numeric'],
            'original_price' => ['required', 'numeric'],
            'special_price' => ['nullable', 'numeric'],
            'cost' => ['nullable', 'numeric'],
            'stock_less' => ['boolean'],
            'allow_back_order' => ['boolean'],
            'show_out_of_stock' => ['boolean'],
            'promotion_price' => ['required_with:in_promotion,promotion_starts_at,promotion_ends_at', 'nullable', 'numeric'],
            'in_promotion' => ['boolean'],
            'promotion_starts_at' => ['nullable', 'date'],
            'promotion_ends_at' => ['nullable', 'date'],
        ];

        $v = Validator::make($partialData, Arr::only($rules, array_keys($partialData)));
        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }

        if ($productType->managedUpdateInfo($partialData, ['edited_by' => Auth::id()])) {
            flash()->success(__('Product :name was updated successfully', ['name' => $productType->name]));
            return redirect()->route('dashboard.admin.catalog.product-type.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }
}

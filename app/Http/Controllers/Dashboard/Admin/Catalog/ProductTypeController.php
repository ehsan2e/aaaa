<?php

namespace App\Http\Controllers\Dashboard\Admin\Catalog;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\ProductCategory;
use App\ProductType;
use App\Rules\ExistingModel;
use App\Supplier;
use App\TaxGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use NovaVoip\Helpers\PaginationGenerator;
use NovaVoip\Interfaces\iPaginationGenerator;

class ProductTypeController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'productTypes';

    /**
     * @var array
     */
    protected $searchableFields = ['product_types.sku', 'product_types.name', 'product_types.supplier_sku', 'product_categories.name', 'product_categories.code', 'suppliers.name'];

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

    protected function getIndexPageData(): array
    {
        return [
            'productCategories' => ProductCategory::select('id', 'code', 'name')->orderBy('name')->get(),
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            [__('ID'), 'product_types.id'],
            [__('Name'), 'product_types.name'],
            [__('Category'), 'product_categories.name'],
            [__('Supplier'), 'suppliers.name'],
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function preCreate()
    {
        $productCategories = ProductCategory::select('id', 'name')->get();
        $types = ProductType::getTypes();
        return view('dashboard.admin.catalog.product-type.pre-create', compact('productCategories', 'types'));
    }

    protected function prePaginationRender(iPaginationGenerator $paginationGenerator): iPaginationGenerator
    {
        return $paginationGenerator->bindQueryParamFilter('category', 'product_types.category_id')
            ->bindQueryParamFilter('category_code', 'product_categories.code')
            ->bindQueryParamFilter('active', ['filter' => 'product_types.active', 'cast' => PaginationGenerator::getCast(PaginationGenerator::CAST_BOOLEAN)]);
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [])
    {
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();
        $taxGroups = TaxGroup::where('active', true)->get();
        return parent::renderForm($view, compact('suppliers', 'taxGroups') + $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \ErrorException
     */
    public function create()
    {
        $type = \Illuminate\Support\Facades\Request::query('type');
        if((!$type) || (!in_array($type, ProductType::TYPES))){
            return $this->preCreate();
        }

        $categoryId = \Illuminate\Support\Facades\Request::query('category');
        if (\Illuminate\Support\Facades\Request::has('category') && isset($categoryId) && (!($productCategory = ProductCategory::find($categoryId)))) {
            return $this->preCreate();
        }

        switch ($type){
            case ProductType::TYPE_SIMPLE:
                return $this->renderForm(sprintf($this->viewPath ?? '%s.%s.create', $this->dashboardPrefix, $this->getViewBasePath()), compact('productCategory', 'type'));
                break;
            case ProductType::TYPE_CONFIGURABLE:
                dd(__FILE__, __LINE__);
                break;
        }
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
        $v = Validator::make([], []);
        $rules = [
            'category_id' => ['nullable', (new ExistingModel('product_categories'))->setMessage(__('Select a valid category'))],
            'sku' => ['required', Rule::unique('product_types')],
            'name' => ['required'],
            'description' => [],
            'picture' => [],
            'active' => ['boolean'],
            'imposes_pre_invoice_negotiation' => ['boolean'],
            'on_sale' => ['boolean'],
            'supplier_id' => ['nullable', (new ExistingModel('suppliers'))->setMessage(__('Select a valid supplier'))],
            'supplier_sku' => [],
            'supplier_share' => ['nullable', 'numeric'],
            'original_price' => ['required', 'numeric'],
            'special_price' => ['nullable', 'numeric'],
            'cost' => ['nullable', 'numeric'],
            'tax_groups' => ['nullable', 'array'],
            'stock_less' => ['boolean'],
            'allow_back_order' => ['boolean'],
            'show_out_of_stock' => ['boolean'],
            'promotion_price' => ['required_with:in_promotion,promotion_starts_at,promotion_ends_at', 'nullable', 'numeric'],
            'in_promotion' => ['boolean'],
            'promotion_starts_at' => ['nullable', 'date'],
            'promotion_ends_at' => ['nullable', 'date'],
            'periodicity' => ['required', Rule::in(ProductType::PERIODS)],
            'upsell_alternatives' => ['required', 'array', 'min:1'],
            'upsell_alternatives.*.amount' => ['required', 'numeric'],
            'upsell_alternatives.*.price' => ['required', 'numeric'],
            'upsell_alternatives.*.cost' => ['nullable', 'numeric'],
            'upsell_alternatives.*.supplier_share' => ['nullable', 'numeric'],
        ];
        $data['tax_groups'] = $data['tax_groups'] ?? [];
        if (isset($data['category_id'])) {
            /** @var ProductCategory $productCategory */
            $productCategory = ProductCategory::find($data['category_id']);
            $rules += $productCategory->getCustomAttributeValidationRules();
            $v->setAttributeNames($productCategory->getCustomAttributeCaptions(true));
            if (isset($data['custom_attributes'])) {
                $data['custom_attributes'] = $productCategory->parseCustomAttributes($data['custom_attributes']);
            }
        }
        $v->setData($data);
        $v->setRules($rules);
        if ($v->fails()) {
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
        $productType->load(['taxGroups']);
        return $this->renderForm('dashboard.admin.catalog.product-type.edit', compact('productType', 'productCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ProductType $productType
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function update(Request $request, ProductType $productType)
    {
        $partialData = $productType->preparePartialData(Auth::user(), $request->except(['_method', '_token']));
        $rules = Arr::only([
            'category_id' => ['nullable', (new ExistingModel('product_categories'))->setMessage(__('Select a valid category'))],
            'name' => ['required'],
            'description' => [],
            'picture' => [],
            'active' => ['boolean'],
            'imposes_pre_invoice_negotiation' => ['boolean'],
            'on_sale' => ['boolean'],
            'supplier_id' => ['nullable', (new ExistingModel('suppliers'))->setMessage(__('Select a valid supplier'))],
            'supplier_sku' => [],
            'supplier_share' => ['nullable', 'numeric'],
            'original_price' => ['required', 'numeric'],
            'special_price' => ['nullable', 'numeric'],
            'cost' => ['nullable', 'numeric'],
            'tax_groups' => ['nullable', 'array'],
            'stock_less' => ['boolean'],
            'allow_back_order' => ['boolean'],
            'show_out_of_stock' => ['boolean'],
            'in_promotion' => ['boolean'],
            'promotion_starts_at' => ['nullable', 'date'],
            'promotion_ends_at' => ['nullable', 'date'],
            'periodicity' => ['required', Rule::in(ProductType::PERIODS)],
            'upsell_alternatives' => ['required', 'array', 'min:1'],
            'upsell_alternatives.*.amount' => ['required', 'numeric'],
            'upsell_alternatives.*.price' => ['required', 'numeric'],
            'upsell_alternatives.*.cost' => ['nullable', 'numeric'],
            'upsell_alternatives.*.supplier_share' => ['nullable', 'numeric'],
        ], array_keys($partialData));

        $v = Validator::make([], []);
        if (isset($productType->category_id)) {
            /** @var ProductCategory $productCategory */
            $productCategory = $productType->category;
            $rules += $productCategory->getCustomAttributeValidationRules();
            $v->setAttributeNames($productCategory->getCustomAttributeCaptions(true));
            if (isset($partialData['custom_attributes'])) {
                $partialData['custom_attributes'] = $productCategory->parseCustomAttributes($partialData['custom_attributes']);
            }
        }
        $v->setData($partialData);
        $v->setRules($rules);
        $v->sometimes('promotion_price', 'required|numeric', function ($input) {
            return (isset($input->in_promotion) && $input->in_promotion) || isset($input->promotion_starts_at) || isset($input->promotion_ends_at);
        });
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

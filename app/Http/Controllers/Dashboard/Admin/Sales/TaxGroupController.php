<?php

namespace App\Http\Controllers\Dashboard\Admin\Sales;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\ProductType;
use App\ProductTypeTaxGroup;
use App\TaxGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function NovaVoip\sortedLanguages;

class TaxGroupController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'taxGroups';

    /**
     * @var
     */
    protected $searchableFields = ['name'];

    /**
     * @var string
     */
    protected $viewBasePath = 'sales.tax-group';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return TaxGroup::query();
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
        return parent::renderForm($view, compact('languages') + $data);
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
        $request->validate([
            'name' => ['required'],
            'amount' => ['required', 'numeric'],
            'active' => ['boolean'],
            'is_percentage' => ['boolean'],
            'name_translations' => ['nullable', 'array'],
        ]);
        if (TaxGroup::createNewTaxGroup(Auth::user(), $data)) {
            flash()->success($insight->message ?? __('Tax group :name was created successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.sales.tax-group.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TaxGroup $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxGroup $taxGroup)
    {
        return $this->renderForm('dashboard.admin.sales.tax-group.edit', compact('taxGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\TaxGroup $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxGroup $taxGroup)
    {
        $data = $request->all();
        $request->validate([
            'name' => ['required'],
            'amount' => ['required', 'numeric'],
            'active' => ['boolean'],
            'is_percentage' => ['boolean'],
            'name_translations' => ['nullable', 'array'],
        ]);

        if ($taxGroup->updateInfo(Auth::user(), $data)) {
            flash()->success($insight->message ?? __('Tax group :name was updated successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.sales.tax-group.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * @param Request $request
     * @param TaxGroup $taxGroup
     * @return \Illuminate\View\View
     * @throws \ErrorException
     */
    public function productTypeIndex(Request $request, TaxGroup $taxGroup)
    {
        $queryBuilder = ProductTypeTaxGroup::join('product_types', 'product_types.id', '=', 'product_type_tax_groups.product_type_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'product_types.category_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'product_types.supplier_id')
            ->select('product_types.*', 'product_type_tax_groups.id as product_type_tax_group_id', 'product_categories.code AS category_code', 'product_categories.name AS category_name', 'suppliers.name as supplier_name')
            ->where('product_type_tax_groups.tax_group_id', $taxGroup->id);

        $paginationGenerator = $this->paginate($request, $queryBuilder)
            ->view(sprintf($this->viewPath ?? '%s.%s.product-type-index', $this->dashboardPrefix, $this->getViewBasePath()))
            ->setCollectionName('productTypes')
            ->setSearchableFields(['product_types.name', 'product_types.sku', 'product_types.supplier_sku', 'product_categories.name', 'suppliers.name'])
            ->cast(ProductType::class);

        return $this->prePaginationRender($paginationGenerator)->render(compact('taxGroup'));
    }

    public function productTypeTaxGroupDestroy(ProductTypeTaxGroup $productTypeTaxGroup)
    {
        $taxGroupId = $productTypeTaxGroup->tax_group_id;
        $productTypeTaxGroup->delete();
        flash()->success(__('Tax group removed from product'));
        return redirect()->route('dashboard.admin.sales.tax-group.product-type.index', ['tax_group' => $taxGroupId]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard\Admin\Sales;

use App\Country;
use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\Rules\ExistingModel;
use App\TaxGroup;
use App\TaxRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxRuleController  extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'taxRules';

    /**
     * @var
     */
    protected $searchableFields = ['countries.name', 'provinces.name'];

    /**
     * @var TaxGroup
     */
    protected $taxGroup;

    /**
     * @var string
     */
    protected $viewBasePath = 'sales.tax-rule';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return TaxRule::join('tax_groups', 'tax_groups.id', '=', 'tax_rules.tax_group_id')
            ->leftJoin('countries', 'countries.code', '=', 'tax_rules.country_code')
            ->leftJoin('provinces', 'provinces.code', '=', 'tax_rules.province_code')
            ->select('tax_rules.*','countries.name as country_name', 'countries.name_translations as country_name_translations', 'provinces.name as province_name', 'provinces.name_translations as province_name_translations')
            ->where('tax_rules.tax_group_id', $this->taxGroup->id);
    }

    protected function getIndexPageData(): array
    {
        return ['taxGroup' => $this->taxGroup];
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [])
    {
        $countries = Country::with(['provinces'])->orderBy('name')->get();
        $taxGroup = $this->taxGroup;
        return parent::renderForm($view, compact('countries', 'taxGroup') + $data);
    }

    /**
     * @param Request $request
     * @param TaxGroup $taxGroup
     * @return \Illuminate\View\View
     * @throws \ErrorException
     */
    public function indexProxy(Request $request, TaxGroup $taxGroup)
    {
        $this->taxGroup = $taxGroup;
        return parent::index($request);
    }

    /**
     * @param TaxGroup $taxGroup
     * @return \Illuminate\Http\Response
     * @throws \ErrorException
     */
    public function createProxy(TaxGroup $taxGroup)
    {
        $this->taxGroup = $taxGroup;
        return parent::create();
    }

    /**
     * @param Request $request
     * @param TaxGroup $taxGroup
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, TaxGroup $taxGroup)
    {
        $data = $request->all();
        $request->validate([
            'country_code' => ['nullable', (new ExistingModel('countries', 'code'))->setMessage(__('Select a valid country'))],
            'province_code' => ['nullable', (new ExistingModel('provinces', 'code'))->setMessage(__('Select a valid province'))],
            'amount' => ['required', 'numeric'],
            'priority' => ['required', 'numeric', 'max:65535'],
            'active' => ['boolean'],
            'is_percentage' => ['boolean'],
        ]);

        if ($taxGroup->defineNewTaxRule(Auth::user(), $data)) {
            flash()->success($insight->message ?? __('Tax rule was created successfully'));
            return redirect()->route('dashboard.admin.sales.tax-rule.index', ['tax_group' => $taxGroup]);
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TaxGroup $taxGroup
     * @param  \App\TaxRule $taxRule
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxGroup $taxGroup, TaxRule $taxRule)
    {
        $this->taxGroup = $taxGroup;
        return $this->renderForm('dashboard.admin.sales.tax-rule.edit', compact('taxRule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaxRule  $taxRule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxGroup $taxGroup, TaxRule $taxRule)
    {
        $data = $request->all();
        $request->validate([
            'country_code' => ['nullable', (new ExistingModel('countries', 'code'))->setMessage(__('Select a valid country'))],
            'province_code' => ['nullable', (new ExistingModel('provinces', 'code'))->setMessage(__('Select a valid province'))],
            'amount' => ['required', 'numeric'],
            'priority' => ['required', 'numeric', 'max:65535'],
            'active' => ['boolean'],
            'is_percentage' => ['boolean'],
        ]);

        if ($taxRule->updateInfo(Auth::user(), $data)) {
            flash()->success($insight->message ?? __('Tax rule was updated successfully'));
            return redirect()->route('dashboard.admin.sales.tax-rule.index', ['tax_group' => $taxGroup]);
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TaxGroup $taxGroup
     * @param  \App\TaxRule $taxRule
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(TaxGroup $taxGroup, TaxRule $taxRule)
    {
        $taxRule->delete();
        flash()->success(__('Tax rule was removed'));
        return redirect()->route('dashboard.admin.sales.tax-rule.index', ['tax_group' => $taxGroup]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard\Admin\Sales;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\TaxGroup;
use Illuminate\Database\Eloquent\Builder;
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
}

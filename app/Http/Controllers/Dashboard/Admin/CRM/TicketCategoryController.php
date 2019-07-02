<?php

namespace App\Http\Controllers\Dashboard\Admin\CRM;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\TicketCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NovaVoip\Helpers\PaginationGenerator;
use NovaVoip\Interfaces\iPaginationGenerator;
use function NovaVoip\sortedLanguages;

class TicketCategoryController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'ticketCategories';

    /**
     * @var array
     */
    protected $searchableFields = [
        'title'
    ];

    /**
     * @var string
     */
    protected $viewBasePath = 'crm.ticket-category';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return TicketCategory::query();
    }

    protected function getIndexPageData(): array
    {
        return ['types' => TicketCategory::getTypes()];
    }

    protected function prePaginationRender(iPaginationGenerator $paginationGenerator): iPaginationGenerator
    {
        return $paginationGenerator->bindQueryParamFilter('type')
            ->bindQueryParamFilter('active', ['cast' => PaginationGenerator::getCast(PaginationGenerator::CAST_BOOLEAN)]);
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
        $types = TicketCategory::getTypes();
        return parent::renderForm($view, compact('languages', 'types') + $data);
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
            'title' => ['required'],
            'type' => ['required', Rule::in(TicketCategory::TYPES)],
            'active' => ['boolean'],
            'title_translations' => ['nullable', 'array'],
        ]);
        if (TicketCategory::createNewTicketCategory($data)) {
            flash()->success($insight->message ?? __('Ticket category :title was created successfully', ['title' => $data['title']]));
            return redirect()->route('dashboard.admin.crm.ticket-category.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TicketCategory $ticketCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(TicketCategory $ticketCategory)
    {
        return $this->renderForm('dashboard.admin.crm.ticket-category.edit', compact('ticketCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\TicketCategory $ticketCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $data = $request->all();
        $request->validate([
            'title' => ['required'],
            'type' => ['required', Rule::in(TicketCategory::TYPES)],
            'active' => ['boolean'],
            'title_translations' => ['nullable', 'array'],
        ]);

        if ($ticketCategory->updateInfo($data)) {
            flash()->success($insight->message ?? __('Ticket category :title was updated successfully', ['title' => $data['title']]));
            return redirect()->route('dashboard.admin.crm.ticket-category.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }
}

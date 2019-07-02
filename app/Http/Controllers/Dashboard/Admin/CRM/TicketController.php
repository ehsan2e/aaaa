<?php

namespace App\Http\Controllers\Dashboard\Admin\CRM;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\Ticket;
use App\TicketCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TicketController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'tickets';

    /**
     * @var array
     */
    protected $searchableFields = ['subject', 'ticket_categories.title', 'u1.name', 'u2.name', 'u3.name', 'u4.name'];

    /**
     * @var string
     */
    protected $viewBasePath = 'crm.ticket';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Ticket::query()
            ->join('ticket_categories', 'ticket_categories.id', '=', 'tickets.category_id')
            ->leftJoin('users as u1', 'u1.id', '=', 'tickets.user_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'tickets.initiator_id')
            ->leftJoin('users as u3', 'u3.id', '=', 'tickets.last_interactor')
            ->leftJoin('users as u4', 'u4.id', '=', 'tickets.assignee')
            ->select([
                'tickets.*',
                'ticket_categories.title as category_title', 'ticket_categories.title_translations as category_title_translations',
                'u1.name as user',
                'u2.name as initiator',
                'u3.name as last_interactor',
                'u4.name as assignee',
            ]);
    }

    protected function renderForm(string $view, array $data = [])
    {
        $ticketCategories = TicketCategory::get();
        return parent::renderForm($view, compact('ticketCategories') + $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}

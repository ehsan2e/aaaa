<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Attachment;
use App\Rules\ExistingModel;
use App\Ticket;
use App\TicketCategory;
use App\TicketFollower;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use NovaVoip\Traits\HandlesFileUpload;

class SupportController extends AbstarctClientController
{
    use HandlesFileUpload;
    /**
     * @var string
     */
    protected $collectionName = 'tickets';

    /**
     * we need custom filter
     * @var array
     */
    //protected $searchableFields = [];

    /**
     * @var string
     */
    protected $viewBasePath = 'support';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return TicketFollower::query()
            ->leftJoin('tickets', 'tickets.id', '=', 'ticket_followers.ticket_id')
            ->leftJoin('ticket_categories', 'ticket_categories.id', '=', 'tickets.category_id')
            ->select(['ticket_followers.id as ticket_follower_id', 'ticket_followers.ticket_id', 'tickets.*', 'ticket_categories.title as category_title', 'ticket_categories.title_translations as category_title_translations'])
            ->where('ticket_followers.user_id', Auth::id())
            ->where('ticket_followers.following', true)
            ->orderBy('ticket_followers.ticket_id', 'desc');
    }

    protected function getSearchableFields(): array
    {
        return [
            'tickets.subject',
            function (Builder $query, string $q) {
                if (($ticketId = Ticket::decryptTicketNumber($q)) !== null) {
                    $query->where('ticket_followers.ticket_id', $ticketId);
                }
            }
        ];
    }

    protected function renderForm(string $view, array $data = [])
    {
        $ticketCategories = TicketCategory::where('type', TicketCategory::TYPE_INCOMING)->where('active', true)->get();
        $urgencies = Ticket::getUrgencies();
        $currentFiles = $this->loadCurrentFiles();
        return parent::renderForm($view, compact('currentFiles', 'ticketCategories', 'urgencies') + $data);
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
        if($request->ajax()){
            return $this->handleFileUpload($request, storage_path('attachments'), ['max:1000']);
        }
        $data = $request->all();
        $existingTicketCategory = new ExistingModel('ticket_categories');
        $existingTicketCategory->where('type', TicketCategory::TYPE_INCOMING)->where('active', true);
        $request->validate([
            'category_id' => ['required', $existingTicketCategory->setMessage(__('Select a valid category'))],
            'urgency' => ['required', Rule::in(Ticket::URGENCY)],
            'subject' => ['required', 'max:500'],
            'message' => ['required', 'max:65535'],
            'attachments' => ['nullable', 'array'],
        ]);

        if (Ticket::createNewIncomingTicket(Auth::user(), $data, $insight)) {
            flash()->success($insight->message ?? __('Ticket was created successfully and will be processed by the support team in a short time'));
            return redirect()->route('dashboard.client.support.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function createEntry(Request $request, Ticket $ticket)
    {
        //
    }
}

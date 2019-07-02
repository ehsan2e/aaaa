<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->subject }}</td>
    <td>{{ $item->category_title }}</td>
    <td>
        <p><b>{{ __('Assignee:') }}</b> {{ $item->assignee ?? '-' }}</p>
        <p><b>{{ __('Initiator:') }}</b> {{ $item->initiator }}</p>
        <p><b>{{ __('Last Interactor:') }}</b> {{ $item->last_interactor ?? '-' }}</p>
        <p><b>{{ __('Owner:') }}</b> {{ $item->user }}</p>
    <td>
        <p><b>{{ __('Urgency:') }}</b> {{ \App\Ticket::getUrgencies()[$item->urgency] ?? $item->urgency }}</p>
        <p><b>{{ __('Status:') }}</b> {{ \App\Ticket::getStatuses()[$item->status] ?? $item->status }}</p>
        <p><b>{{ __('Closed:') }}</b> <i class="fa fa-{{ $item->closed ? 'check text-success' : 'remove text-danger' }}"></i></p>
        <p><b>{{ __('Reopened:') }}</b> <i class="fa fa-{{ $item->reopened ? 'check text-success' : 'remove text-danger' }}"></i></p>
        <p><b>{{ __('Reopen Allowed:') }}</b> <i class="fa fa-{{ $item->reopen_allowed ? 'check text-success' : 'remove text-danger' }}"></i></p>
        <p><b>{{ __('Created At:') }}</b> {{ $item->created_at }}</p>
        <p><b>{{ __('Last Support Response:') }}</b> {{ $item->last_interaction_at ?? '-' }}</p>
        <p><b>{{ __('Last User Response:') }}</b> {{ $item->last_response_at }}</p>
        <p><b>{{ __('Last Update:') }}</b> {{ $item->updated_at }}</p>
    </td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ /*route('dashboard.admin.crm.ticket.show', ['ticket_category' => $item])*/ '#' }}">{{ __('Show Ticket') }}</a>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.crm.ticket.edit', ['ticket_category' => $item]) }}">{{ __('Edit') }}</a>
    </td>
</tr>
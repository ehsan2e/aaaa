<tr>
    <td>{{ $item->ticket_number }}</td>
    <td>{{ $item->subject }}</td>
    <td>{{ \NovaVoip\translateEntity($item, 'category_title',null, false, '', function($jsonString){return json_decode($jsonString);}) }}</td>
    <td>
        @if($item->status == \App\Ticket::STATUS_WAITING_RESPONSE)
            {{ __('Waiting for your response') }}
        @else
            {{ __('In Progress') }}
        @endif
        <div class="badge badge-secondary" title="{{ __('Urgency') }}" data-toggle="tooltip">{{ \App\Ticket::getUrgencies()[$item->urgency] ?? $item->urgency }}</div>
        @if($item->closed)
            <i class="fa fa-circle text-danger" title="{{ __('Closed') }}"  data-toggle="tooltip"></i>
        @else
            <i class="fa fa-circle text-success" title="{{ __('Open') }}"  data-toggle="tooltip"></i>
        @endif
    </td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.client.support.show', ['ticket' => $item]) }}">{{ __('View') }}</a>
    </td>
</tr>
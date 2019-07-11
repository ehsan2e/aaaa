<tr>
    <td>{{ $item->order_number }}</td>
    <td>{{ $item->sub_total }}</td>
    <td>{{ $item->discount }}</td>
    <td>{{ $item->tax }}</td>
    <td>{{ $item->grand_total }}</td>
    <td>
        @if($item->needs_negotiation)
            {{ __('Waiting for interview') }}
        @elseif($item->can_be_invoiced && ($item->invoices_issued_count === 0))
            {{ __('Pending invoice') }}
        @elseif($item->is_cancelled))
            {{ __('Cancelled') }}
            @date($item->cancelled_at, 'Y-m-d H:i')
        @elseif($item->is_paid))
            {{ __('Paid') }}
            @date($item->paid_at, 'Y-m-d H:i')
        @endif

    </td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.client.order.show', ['order' => $item]) }}">{{ __('View') }}</a>
    </td>
</tr>
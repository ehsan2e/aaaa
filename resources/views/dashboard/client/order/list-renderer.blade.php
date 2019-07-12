<tr>
    <td>{{ $item->order_number }}</td>
    <td>{{ $item->sub_total }}</td>
    <td>{{ $item->discount }}</td>
    <td>{{ $item->tax }}</td>
    <td>{{ $item->grand_total }}</td>
    <td>@component('dashboard.client.order.status', ['order' => $item]) @endcomponent</td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.client.order.show', ['order' => $item]) }}">{{ __('View') }}</a>
    </td>
</tr>
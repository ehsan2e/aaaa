<tr>
    <td>{{ $item->invoice_number }}</td>
    <td>{{ $item->sub_total }}</td>
    <td>{{ $item->discount }}</td>
    <td>{{ $item->tax }}</td>
    <td>{{ $item->grand_total }}</td>
    <td>@component('dashboard.client.invoice.status', ['invoice' => $item]) @endcomponent</td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.client.invoice.show', ['invoice' => $item]) }}">{{ __('View') }}</a>
        @if($item->payable())
            <a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal"
                href = "{{ route('dashboard.client.invoice.pay', ['invoice' => $item]) }}"
               data-can-use-credit="{{ $item->can_be_paid_by_credit ? 'yes' : 'no' }}"
            >{{ __('Pay') }}</a>
        @endif
    </td>
</tr>
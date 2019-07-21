<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->payment_number }}</td>
    <td>{{ $item->gateway }}</td>
    <td>{{ $item->amount }}</td>
    <td>{{ $item->reference_number ?? '-' }}</td>
    <td>{{ $item->status_caption }}</td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.sales.payment.show', ['payment' => $item]) }}">{{ __('View') }}</a>
    </td>
</tr>
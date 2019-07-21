<tr>
    <td>{{ $item->payment_number }}</td>
    <td>{{ $item->amount }}</td>
    <td>{{ $item->reference_number ?? '-' }}</td>
    <td>{{ $item->status_caption }}</td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>
        @if($item->status === \App\Payment::STATUS_NEW)
            <button type="button" class="btn btn-primary btn-sm">{{ __('Proceed') }}</button>
        @endif
    </td>
</tr>
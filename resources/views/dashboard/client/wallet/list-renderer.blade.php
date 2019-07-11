<tr>
    <td>{{ $item->old_balance }}</td>
    <td><span class="text-{{ $item->amount < 0 ? 'danger' : 'success' }}">{{ $item->amount }}</span></td>
    <td>{{ $item->new_balance }}</td>
    <td>{{ $item->type ?? '-' }}</td>
    <td>@date($item->created_at, 'Y-m-d H:i')</td>
    <td>{{ $item->description ?? '-' }}</td>

</tr>
<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->name }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td><i class="fa fa-{{ $item->user_id ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td>{{ $item->login_email ?? '-' }}</td>
    <td>@date($item->created_at)</td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.crm.client.show', ['client' => $item]) }}">{{ __('Show') }}</a>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.crm.client.edit', ['client' => $item]) }}">{{ __('Edit') }}</a>
    </td>
</tr>
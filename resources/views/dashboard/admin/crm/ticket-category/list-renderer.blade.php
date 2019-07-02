<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->title }}</td>
    <td>{{ $item->type_caption }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ /*route('dashboard.admin.crm.ticket-category.show', ['ticket_category' => $item])*/ '#' }}">{{ __('Show Tickets') }}</a>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.crm.ticket-category.edit', ['ticket_category' => $item]) }}">{{ __('Edit') }}</a>
    </td>
</tr>
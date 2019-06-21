<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->code }}</td>
    <td>{{ $item->name }}</td>
    <td>{{ $item->parent_name ?? '-' }}</td>
    <td>{{ $item->address ?? '-' }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.catalog.product-category.edit', ['product_category' => $item]) }}">{{ __('Edit') }}</a>
    </td>
</tr>
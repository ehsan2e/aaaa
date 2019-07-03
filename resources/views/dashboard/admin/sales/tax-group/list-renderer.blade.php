<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->name }}</td>
    <td>{{ $item->amount }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td><i class="fa fa-{{ $item->is_percentage ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.sales.tax-group.edit', ['tax_group' => $item]) }}">{{ __('Edit') }}</a>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.sales.tax-rule.index', ['tax_group' => $item]) }}">{{ __('Rules') }}</a>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.sales.tax-group.product-type.index', ['tax_group' => $item]) }}">{{ __('Product Types') }}</a>
    </td>
</tr>
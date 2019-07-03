<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->country_name ?? '*' }}</td>
    <td>{{ $item->province_name ?? '*' }}</td>
    <td>{{ $item->priority }}</td>
    <td>{{ $item->amount }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td><i class="fa fa-{{ $item->is_percentage ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.sales.tax-rule.edit', ['tax_group' => $taxGroup, 'tax_rule' => $item]) }}">{{ __('Edit') }}</a>
        <a class="btn btn-sm btn-danger remove-rule"
           href="{{ route('dashboard.admin.sales.tax-rule.destroy', ['tax_group' => $taxGroup, 'tax_rule' => $item]) }}">{{ __('Delete') }}</a>
    </td>
</tr>
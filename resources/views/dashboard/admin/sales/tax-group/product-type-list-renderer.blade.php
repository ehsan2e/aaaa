<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->sku }}</td>
    <td>{{ $item->name }}</td>
    <td>
        @if($item->price === $item->original_price)
            {{ $item->price }}
        @else
            <span class="text-danger" style="text-decoration: line-through">{{ $item->original_price }}</span>
            <span>{{ $item->price }}</span>
        @endif
    </td>
    <td>{{ $item->category_name ?? '-' }}</td>
    <td>{{ $item->supplier_name ?? '-' }}</td>
    <td>{{ $item->supplier_sku ?? '-' }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.catalog.product-type.edit', ['product_type' => $item]) }}">{{ __('view product') }}</a>
        <a class="btn btn-sm btn-danger remove-product-type-tax-group"
           href="{{ route('dashboard.admin.sales.product-type-tax-group.destroy', ['product_type_tax_group' => $item->product_type_tax_group_id]) }}">{{ __('remove') }}</a>
    </td>
</tr>
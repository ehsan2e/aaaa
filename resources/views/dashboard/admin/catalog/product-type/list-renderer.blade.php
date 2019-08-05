<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->type_caption }}</td>
    <td>{{ $item->sku }}</td>
    <td>{{ $item->name }}</td>
    <td>{{ $item->category_name ?? '-' }}</td>
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
           href="{{ route('dashboard.admin.catalog.product-type.edit', ['product_type' => $item]) }}">{{ __('Edit') }}</a>
    </td>
</tr>
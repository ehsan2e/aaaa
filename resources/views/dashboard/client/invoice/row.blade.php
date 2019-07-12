@php
    $parentId = $parentId ?? null;
    $level = $level ?? 0;
    $extended = $extended ?? false
@endphp
@foreach($items as $item)
    @continue($parentId ? ($item->parent_id != $parentId): isset($item->parent_id))
    @if(isset($item->productType))
        @switch($item->productType->category->code)
            @case(config('nova.box_category_code'))
            <tr class="cart-invoice-row .cart-invoice-row-level-{{$level}}@if($level > 0) cart-invoice-row-sub-item @endif">
                <td>
                    <span style="padding-left:{{$level * 50}}px;">{!! __('Box capable of serving <b>:employee_number</b> employee/s', ['employee_number' => $item->extra_information['employee_number']]) !!}</span>
                </td>
                <td>{{ $item->amount  }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->sub_total }}</td>
                @if($extended)
                    <td>{{ $item->discount }}</td>
                    <td>{{ $item->tax }}</td>
                    <td>{{ $item->grand_total }}</td>
                @endif
            </tr>
            @component('dashboard.client.invoice.row', ['items' => $items, 'parentId' => $item->id, 'level' => $level + 1, 'extended' => $extended, 'invoice' => $invoice]) @endcomponent
            @break
            @default
            <tr class="cart-invoice-row .cart-invoice-row-level-{{$level}}@if($level > 0) cart-invoice-row-sub-item @endif">
                <td>
                    <span style="padding-left:{{$level * 50}}px;">{{ $item->productType->name  }}</span>
                </td>
                <td>{{ $item->amount  }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->sub_total }}</td>
                @if($extended)
                    <td>{{ $item->discount }}</td>
                    <td>{{ $item->tax }}</td>
                    <td>{{ $item->grand_total}}</td>
                @endif
            </tr>
            @component('dashboard.client.invoice.row', ['items' => $items, 'parentId' => $item->id, 'level' => $level + 1, 'extended' => $extended, 'invoice' => $invoice]) @endcomponent
        @endswitch
    @else
        @switch($item->tag)
            @case('charge_wallet')
            <tr class="cart-invoice-row .cart-invoice-row-level-{{$level}}@if($level > 0) cart-invoice-row-sub-item @endif">
                <td>
                    <span style="padding-left:{{$level * 50}}px;">{{ $item->description }}</span>
                </td>
                <td>-</td>
                <td>-</td>
                <td>{{ $item->sub_total }}</td>
                @if($extended)
                    <td>-</td>
                    <td>-</td>
                    <td>{{ $item->grand_total}}</td>
                @endif
            </tr>
            @break
            @default
            <tr class="cart-invoice-row .cart-invoice-row-level-{{$level}}@if($level > 0) cart-invoice-row-sub-item @endif">
                <td>
                    <span style="padding-left:{{$level * 50}}px;">{{ $item->description }}</span>
                </td>
                <td>{{ $item->amount  }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->sub_total }}</td>
                @if($extended)
                    <td>{{ $item->discount }}</td>
                    <td>{{ $item->tax }}</td>
                    <td>{{ $item->grand_total}}</td>
                @endif
            </tr>
            @component('dashboard.client.invoice.row', ['items' => $items, 'parentId' => $item->id, 'level' => $level + 1, 'extended' => $extended, 'invoice' => $invoice]) @endcomponent
        @endswitch
    @endif
@endforeach
@php
    $parentId = $parentId ?? null;
    $level = $level ?? 0;
    $extended = $extended ?? false
@endphp
@foreach($items as $item)
    @continue($parentId ? ($item->parent_id != $parentId): isset($item->parent_id))
    @switch($item->productType->category->code)
        @case(config('nova.box_category_code'))
        <tr class="cart-invoice-row .cart-invoice-row-level-{{$level}}@if($level > 0) cart-invoice-row-sub-item @endif">
            <td>
                <span style="padding-left:{{$level * 50}}px;">{!! __('Box capable of serving <b>:employee_number</b> employee/s', ['employee_number' => $item->extra_information['employee_number']]) !!}</span>
            </td>
            <td>{{ $item->amount  }}</td>
            <td>
                {{ $item->price }}
                @if((!isset($item->negotiated_at)) && $order->needs_negotiation && $item->productType->imposes_pre_invoice_negotiation)
                    <i class="fa fa-info-circle text-info"
                       title="{{ __('Final price will be determined through an interview after order gets placed') }}"
                       data-toggle="tooltip"></i>
                @endif
            </td>
            <td>{{ $item->sub_total }}</td>
            @if($extended)
                <td>{{ $item->discount }}</td>
                <td>{{ $item->tax }}</td>
                <td>{{ $item->grand_total }}</td>
            @endif
        </tr>
        @component('dashboard.client.order.row', ['items' => $items, 'parentId' => $item->id, 'level' => $level + 1, 'extended' => $extended, 'order' => $order]) @endcomponent
        @break
        @default
        <tr class="cart-invoice-row .cart-invoice-row-level-{{$level}}@if($level > 0) cart-invoice-row-sub-item @endif">
            <td>
                <span style="padding-left:{{$level * 50}}px;">{{ $item->productType->name  }}</span>
            </td>
            <td>{{ $item->amount  }}</td>
            <td>
                {{ $item->price }}
                @if((!isset($item->negotiated_at)) && $order->needs_negotiation && $item->productType->imposes_pre_invoice_negotiation)
                    <i class="fa fa-info-circle text-info"
                       title="{{ __('Final price will be determined through an interview after order gets placed') }}"
                       data-toggle="tooltip"></i>
                @endif
            </td>
            <td>{{ $item->sub_total }}</td>
            @if($extended)
                <td>{{ $item->discount }}</td>
                <td>{{ $item->tax }}</td>
                <td>{{ $item->grand_total}}</td>
            @endif
        </tr>
        @component('dashboard.client.order.row', ['items' => $items, 'parentId' => $item->id, 'level' => $level + 1, 'extended' => $extended, 'order' => $order]) @endcomponent
    @endswitch
@endforeach
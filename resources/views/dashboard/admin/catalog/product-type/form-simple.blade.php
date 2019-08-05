@php
    $activeTab = $activeTab ?? 'product-general';
@endphp
@csrf
<div class="row">
    <div class="col-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
             aria-orientation="vertical">
            <a class="nav-link @if($activeTab === 'product-general') active @endif" id="product-general-tab"
               data-toggle="pill"
               href="#product-general" role="tab" aria-controls="product-general"
               aria-selected="true">@someError(['category_id', 'sku', 'name', 'description', 'picture', 'active',
                'on_sale', 'appears_in_listing']) <i class="fa fa-warning text-danger"></i> @endSomeError {{ __('General') }}</a>
            <a class="nav-link  @if($activeTab === 'product-supplier') active @endif" id="product-supplier-tab"
               data-toggle="pill"
               href="#product-supplier" role="tab" aria-controls="product-supplier"
               aria-selected="false">@someError(['supplier_id', 'supplier_sku', 'supplier_share']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError{{ __('Supplier') }}</a>
            <a class="nav-link  @if($activeTab === 'product-price') active @endif" id="product-price-tab"
               data-toggle="pill"
               href="#product-price" role="tab" aria-controls="product-price"
               aria-selected="false">@someError(['original_price', 'special_price', 'upsell_alternatives', 'upsell_alternatives.*', 'cost', 'tax_groups']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError{{ __('Price') }}</a>
            <a class="nav-link  @if($activeTab === 'product-inventory') active @endif" id="product-inventory-tab"
               data-toggle="pill"
               href="#product-inventory" role="tab" aria-controls="product-inventory"
               aria-selected="false">@someError(['stock_less', 'allow_back_order', 'show_out_of_stock']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError{{ __('Inventory') }}</a>
            <a class="nav-link  @if($activeTab === 'product-promotion') active @endif" id="product-promotion-tab"
               data-toggle="pill"
               href="#product-promotion" role="tab" aria-controls="product-promotion"
               aria-selected="false">@someError(['promotion_price', 'in_promotion', 'promotion_starts_at',
                'promotion_ends_at']) <i class="fa fa-warning text-danger"></i> @endSomeError{{ __('Promotion') }}</a>
            @if(isset($productCategory))
                <a class="nav-link  @if($activeTab === 'product-custom-attributes') active @endif"
                   id="product-custom-attributes-tab" data-toggle="pill"
                   href="#product-custom-attributes" role="tab"
                   aria-controls="product-custom-attributes"
                   aria-selected="false">@someError(array_merge(['custom_attributes'], array_map(function($item){return
                    'custom_attributes.' . $item['name'];}, $productCategory->custom_attributes ?? []))) <i
                            class="fa fa-warning text-danger"></i>
                    @endSomeError{{ __('Custom Attributes') }}</a>
            @endif
        </div>
    </div>
    <div class="col-9">
        <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade @if($activeTab === 'product-general') show active @endif" id="product-general"
                 role="tabpanel"
                 aria-labelledby="product-general-tab">
                <input type="hidden" name="type" value="{{ $type }}">
                @if(isset($productCategory))
                    <input type="hidden" name="category_id" value="{{ $productCategory->id }}">
                    @error('category_id')
                    <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror
                @endif
                @component('dashboard.components.readonly-text', ['name' => 'category_name', 'defaultValue' => $productCategory->name ?? __('Without category')]){{ __('Product Category') }}@endcomponent
                @component('dashboard.components.readonly-text', ['name' => 'type_name', 'defaultValue' => $types[$type] ]){{ __('Type') }}@endcomponent
                @component('dashboard.components.' . (isset($productType) ? 'readonly-text' : 'input-box'), ['name' => 'sku', 'model' => $productType ?? null]){{ __('SKU') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'name', 'model' => $productType ?? null]){{ __('Name') }}@endcomponent
                @component('dashboard.components.textarea', ['name' => 'description', 'model' => $productType ?? null]){{ __('Description') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'picture', 'model' => $productType ?? null]){{ __('Picture') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'active', 'model' => $productType ?? null, 'default' => true]){{ __('Active') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'imposes_pre_invoice_negotiation', 'model' => $productType ?? null]){{ __('Impose Pre Invoice negotiation') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'on_sale', 'model' => $productType ?? null, 'default' => true]){{ __('On Sale') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'appears_in_listing', 'model' => $productType ?? null, 'default' => true]){{ __('Appears in lists') }}@endcomponent
            </div>
            <div class="tab-pane fade @if($activeTab === 'product-supplier') show active @endif" id="product-supplier"
                 role="tabpanel"
                 aria-labelledby="product-supplier-tab">
                @component('dashboard.components.select', ['name' => 'supplier_id', 'model' => $productType ?? null])
                    @slot('options')
                        <option value="">{{ __('Without supplier') }}</option>
                        @foreach($suppliers as $supplier)
                            @component('dashboard.components.option', ['value'=>$supplier->id, 'selectedValue' => old('category_id', $productType->supplier_id ?? '')]){{ $supplier->name }}@endcomponent
                        @endforeach
                    @endslot
                    {{ __('Supplier') }}
                @endcomponent
                @component('dashboard.components.input-box', ['name' => 'supplier_sku', 'model' => $productType ?? null]){{ __('Supplier SKU') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'supplier_share', 'model' => $productType ?? null]){{ __('Supplier Share') }}@endcomponent

            </div>
            <div class="tab-pane fade @if($activeTab === 'product-price') show active @endif" id="product-price"
                 role="tabpanel"
                 aria-labelledby="product-price-tab">
                @component('dashboard.components.input-box', ['name' => 'original_price', 'model' => $productType ?? null]){{ __('Original Price') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'special_price', 'model' => $productType ?? null]){{ __('Special Price') }}@endcomponent
                @component('dashboard.components.select', ['name' => 'periodicity', 'model' => $productType ?? null, 'items' => \App\ProductType::getPeriods(), 'defaultValue' => \App\ProductType::PERIODICITY_LIFETIME])
                    {{ __('Periodicity') }}
                @endcomponent
                <div id="period-price" class="form-group row"
                     @if(old('periodicity', $productType->periodicity ?? \App\ProductType::PERIODICITY_LIFETIME) == \App\ProductType::PERIODICITY_LIFETIME) style="display: none" @endif>
                    <div class="col-md-4 text-md-right">{{ __('Upsell Alternatives') }}</div>
                    <div class="col-md-8">
                        <table class="table table-striped" id="upsell-alternatives">
                            <thead>
                            <tr>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Cost') }}</th>
                                <th>{{ __('Supplier Share') }}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(old('upsell_alternatives',$productType->upsell_alternatives ?? [['amount' => '1', 'price' => '', 'cost' => '', 'supplier_share' => '']]) as  $c => $upsellAlternative)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control  @error('upsell_alternatives.' . $c .'.amount') is-invalid @enderror"
                                               @if($loop->index === 0) readonly @endif
                                        name="upsell_alternatives[{{$loop->index}}][amount]"
                                               value="{{ $upsellAlternative['amount'] }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control @error('upsell_alternatives.' . $c .'.price') is-invalid @enderror"
                                               @if($loop->index === 0) readonly @endif
                                        name="upsell_alternatives[{{$loop->index}}][price]"
                                               value="{{ $upsellAlternative['price'] }}"
                                               @if($loop->index === 0) id="minimum-amount-price" @endif
                                        >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control @error('upsell_alternatives.' . $c .'.cost') is-invalid @enderror"
                                               @if($loop->index === 0) readonly @endif
                                        name="upsell_alternatives[{{$loop->index}}][cost]"
                                               value="{{ $upsellAlternative['cost'] }}"
                                               @if($loop->index === 0) id="minimum-amount-cost" @endif
                                        >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control @error('upsell_alternatives.' . $c .'.supplier_share') is-invalid @enderror"
                                               @if($loop->index === 0) readonly @endif
                                        name="upsell_alternatives[{{$loop->index}}][supplier_share]"
                                               value="{{ $upsellAlternative['supplier_share'] }}"
                                               @if($loop->index === 0) id="minimum-amount-supplier_share" @endif
                                        >
                                    </td>
                                    <td>
                                        @unless($loop->index === 0)
                                            <button type="button"
                                                    class="btn btn-danger btn-sm remove-upsell-alternative"
                                                    title="{{ __('Remove') }}" data-toggle="tooltip">
                                                <i class="fa fa-remove"></i>
                                            </button>
                                        @endunless
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="clearfix">
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary btn-sm" id="add-upsell-alternative"><i
                                            class="fa fa-plus"></i>{{ __('Add alternative') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @component('dashboard.components.input-box', ['name' => 'cost', 'model' => $productType ?? null]){{ __('Cost') }}@endcomponent
                @component('dashboard.components.select', ['name' => 'tax_groups', 'model' => $productType ?? null, 'multiple' => true, 'modelKey' => 'taxGroups', 'valueParser' => function($data){ return is_a($data, \Illuminate\Support\Collection::class) ? $data->pluck('id')->toArray() : (is_array($data) ? $data : []);}])
                    @slot('options')
                        @foreach($taxGroups as $taxGroup)
                            @component('dashboard.components.option', ['value'=>$taxGroup->id, 'selectedValue' => old('tax_groups', isset($productType->taxGroups) ? ($productType->taxGroups->pluck('id')->toArray()) : [])]){{ \NovaVoip\translateEntity($taxGroup) }}@endcomponent
                        @endforeach
                    @endslot
                    {{ __('Tax Group') }}
                @endcomponent
            </div>
            <div class="tab-pane fade @if($activeTab === 'product-inventory') show active @endif" id="product-inventory"
                 role="tabpanel"
                 aria-labelledby="product-inventory-tab">
                @component('dashboard.components.readonly-text', ['name' => 'stock', 'model' => $productType ?? null, 'defaultValue' => '0']){{ __('Stock') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'stock_less', 'model' => $productType ?? null]){{ __('Stock is not applicable') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'allow_back_order', 'model' => $productType ?? null]){{ __('Allow back order') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'show_out_of_stock', 'model' => $productType ?? null]){{ __('Show even when out of stock') }}@endcomponent
            </div>
            <div class="tab-pane fade @if($activeTab === 'product-promotion') show active @endif" id="product-promotion"
                 role="tabpanel"
                 aria-labelledby="product-promotion-tab">
                @component('dashboard.components.input-box', ['name' => 'promotion_price', 'model' => $productType ?? null]){{ __('Promotion Price') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'in_promotion', 'model' => $productType ?? null]){{ __('Product is being promoted regardless of the dates constraints') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'promotion_starts_at', 'model' => $productType ?? null]){{ __('Promotion Starts At') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'promotion_ends_at', 'model' => $productType ?? null]){{ __('Promotion Ends At') }}@endcomponent

            </div>
            @if(isset($productCategory))
                <div class="tab-pane fade @if($activeTab === 'product-custom-attributes') show active @endif"
                     id="product-custom-attributes" role="tabpanel"
                     aria-labelledby="product-custom-attributes-tab">
                    @forelse($productCategory->custom_attributes ?? [] as $customAttribute)
                        @switch($customAttribute['type'])
                            @case('decimal')
                            @case('integer')
                            @case('picture')
                            @case('price')
                            @case('string')
                            @component('dashboard.components.input-box', ['name' => 'custom_attributes[' . $customAttribute['name'] . ']', 'model' => $productType->custom_attributes ?? null, 'modelKey' => $customAttribute['name']]){{ \NovaVoip\translateEntity($customAttribute, 'caption', 'captions', true) }}@endcomponent
                            @break
                            @case('lookup')
                            @component('dashboard.components.select', ['name' => 'custom_attributes[' . $customAttribute['name'] . ']', 'model' => $productType->custom_attributes ?? null, 'modelKey' => $customAttribute['name']])
                                @slot('options')
                                    @foreach($customAttribute['lookupValues'] as $lookupValue)
                                        @component('dashboard.components.option', ['value'=>$lookupValue['value'], 'selectedValue' => old('custom_attributes.' . $customAttribute['name'], $productType->custom_attributes[$customAttribute['name']] ?? '')]){{ \NovaVoip\translateEntity($lookupValue, 'caption', 'captions', true) }}@endcomponent
                                    @endforeach
                                @endslot
                                {{ \NovaVoip\translateEntity($customAttribute, 'caption', 'captions', true) }}
                            @endcomponent
                            @break
                            @case('boolean')
                            @component('dashboard.components.check-box', ['name' => 'custom_attributes[' . $customAttribute['name'] . ']', 'model' => $productType->custom_attributes ?? null, 'modelKey' => $customAttribute['name']]){{ \NovaVoip\translateEntity($customAttribute, 'caption', 'captions', true) }}@endcomponent
                            @break
                            @default
                            <div class="alert alert-danger">No renderer for {{ $customAttribute['type'] }}</div>
                        @endswitch
                    @empty
                        <div>{{ __('Category has no custom attribute') }}</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>

@push('before-body-ends')
    <script>
        (function ($) {
            var $upsellAlternativeTable = $('#upsell-alternatives');
            var cnt = parseInt('{{ count(old('upsell_alternatives', $productType->upsell_alternatives ?? [['amount' => '1', 'price' => '']])) }}', 10);
            $('#add-upsell-alternative').on('click', function () {
                var j = cnt;
                cnt++
                html = '<tr><td><input type="text" class="form-control" name="upsell_alternatives[' + j + '][amount]"></td>' +
                    '<td><input type="text" class="form-control" name="upsell_alternatives[' + j + '][price]"></td>' +
                    '<td><input type="text" class="form-control" name="upsell_alternatives[' + j + '][cost]"></td>' +
                    '<td><input type="text" class="form-control" name="upsell_alternatives[' + j + '][supplier_share]"></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-upsell-alternative" title="{{ __('Remove') }}" data-toggle="tooltip"><i class="fa fa-remove"></i></button></td></tr>';
                $upsellAlternativeTable.find('tbody').append(html)
            });
            $upsellAlternativeTable.on('click', '.remove-upsell-alternative', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });
            $('#periodicity').on('change', function () {
                if ($(this).val() == '{{ \App\ProductType::PERIODICITY_LIFETIME }}') {
                    $('#period-price').hide();
                } else {
                    $('#period-price').show();
                }
            });
            $('#original_price, #special_price').on('keyup', function () {
                var originalPrice = $('#original_price').val();
                var specialPrice = $('#special_price').val();
                $('#minimum-amount-price').val(specialPrice == '' ? originalPrice : specialPrice);
            });
            $('#cost').on('keyup', function () {
                $('#minimum-amount-cost').val($(this).val());
            });
            $('#supplier_share').on('keyup', function () {
                $('#minimum-amount-supplier_share').val($(this).val());
            });
        })(jQuery)
    </script>
@endpush

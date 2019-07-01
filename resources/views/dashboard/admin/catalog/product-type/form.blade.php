@csrf
<div class="row">
    <div class="col-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
             aria-orientation="vertical">
            <a class="nav-link active" id="product-general-tab" data-toggle="pill"
               href="#product-general" role="tab" aria-controls="product-general"
               aria-selected="true">@someError(['category_id', 'sku', 'name', 'description', 'picture', 'active',
                'on_sale']) <i class="fa fa-warning text-danger"></i> @endSomeError {{ __('General') }}</a>
            <a class="nav-link" id="product-supplier-tab" data-toggle="pill"
               href="#product-supplier" role="tab" aria-controls="product-supplier"
               aria-selected="false">@someError(['supplier_id', 'supplier_sku', 'supplier_share']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError{{ __('Supplier') }}</a>
            <a class="nav-link" id="product-price-tab" data-toggle="pill"
               href="#product-price" role="tab" aria-controls="product-price"
               aria-selected="false">@someError(['original_price', 'special_price', 'cost']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError{{ __('Price') }}</a>
            <a class="nav-link" id="product-inventory-tab" data-toggle="pill"
               href="#product-inventory" role="tab" aria-controls="product-inventory"
               aria-selected="false">@someError(['stock_less', 'allow_back_order', 'show_out_of_stock']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError{{ __('Inventory') }}</a>
            <a class="nav-link" id="product-promotion-tab" data-toggle="pill"
               href="#product-promotion" role="tab" aria-controls="product-promotion"
               aria-selected="false">@someError(['promotion_price', 'in_promotion', 'promotion_starts_at',
                'promotion_ends_at']) <i class="fa fa-warning text-danger"></i> @endSomeError{{ __('Promotion') }}</a>
            @if(isset($productCategory))
                <a class="nav-link" id="product-custom-attributes-tab" data-toggle="pill"
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
            <div class="tab-pane fade show active" id="product-general" role="tabpanel"
                 aria-labelledby="product-general-tab">
                @if(isset($productCategory))
                    <input type="hidden" name="category_id" value="{{ $productCategory->id }}">
                    @error('category_id')
                    <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror
                @endif
                @component('dashboard.components.readonly-text', ['name' => 'category_name', 'model' => $productType ?? null, 'defaultValue' => $productCategory->name ?? __('Without category')]){{ __('Product Category') }}@endcomponent
                @component('dashboard.components.' . (isset($productType) ? 'readonly-text' : 'input-box'), ['name' => 'sku', 'model' => $productType ?? null]){{ __('SKU') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'name', 'model' => $productType ?? null]){{ __('Name') }}@endcomponent
                @component('dashboard.components.textarea', ['name' => 'description', 'model' => $productType ?? null]){{ __('Description') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'picture', 'model' => $productType ?? null]){{ __('Picture') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'active', 'model' => $productType ?? null]){{ __('Active') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'on_sale', 'model' => $productType ?? null]){{ __('On Sale') }}@endcomponent
            </div>
            <div class="tab-pane fade" id="product-supplier" role="tabpanel"
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
            <div class="tab-pane fade" id="product-price" role="tabpanel"
                 aria-labelledby="product-price-tab">
                @component('dashboard.components.input-box', ['name' => 'original_price', 'model' => $productType ?? null]){{ __('Original Price') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'special_price', 'model' => $productType ?? null]){{ __('Special Price') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'cost', 'model' => $productType ?? null]){{ __('Cost') }}@endcomponent
            </div>
            <div class="tab-pane fade" id="product-inventory" role="tabpanel"
                 aria-labelledby="product-inventory-tab">
                @component('dashboard.components.readonly-text', ['name' => 'stock', 'model' => $productType ?? null, 'defaultValue' => '0']){{ __('Stock') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'stock_less', 'model' => $productType ?? null]){{ __('Stock is not applicable') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'allow_back_order', 'model' => $productType ?? null]){{ __('Allow back order') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'show_out_of_stock', 'model' => $productType ?? null]){{ __('Show even when out of stock') }}@endcomponent
            </div>
            <div class="tab-pane fade" id="product-promotion" role="tabpanel"
                 aria-labelledby="product-promotion-tab">
                @component('dashboard.components.input-box', ['name' => 'promotion_price', 'model' => $productType ?? null]){{ __('Promotion Price') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'in_promotion', 'model' => $productType ?? null]){{ __('Product is being promoted regardless of the dates constraints') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'promotion_starts_at', 'model' => $productType ?? null]){{ __('Promotion Starts At') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'promotion_ends_at', 'model' => $productType ?? null]){{ __('Promotion Ends At') }}@endcomponent

            </div>
            @if(isset($productCategory))
                <div class="tab-pane fade" id="product-custom-attributes" role="tabpanel"
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

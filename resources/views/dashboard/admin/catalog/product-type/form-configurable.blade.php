@php
    $activeTab = $activeTab ?? 'product-related-products';
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
                'on_sale', 'appears_in_listing']) <i class="fa fa-warning text-danger"></i>
                @endSomeError {{ __('General') }}</a>
            <a class="nav-link @if($activeTab === 'product-settings') active @endif" id="product-settings-tab"
               data-toggle="pill"
               href="#product-settings" role="tab" aria-controls="product-settings"
               aria-selected="true">@someError(['complex_settings.configurable_attributes']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError {{ __('Settings') }}</a>
            <a class="nav-link @if($activeTab === 'product-related-products') active @endif"
               id="product-related-products-tab"
               data-toggle="pill"
               href="#product-related-products" role="tab" aria-controls="product-related-products"
               aria-selected="true">@someError(['related_products', 'default_product']) <i
                        class="fa fa-warning text-danger"></i> @endSomeError {{ __('Related Products') }}</a>
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
                @component('dashboard.components.readonly-text', ['name' => 'category_name', 'model' => $productType ?? null, 'defaultValue' => $productCategory->name ?? __('Without category')]){{ __('Product Category') }}@endcomponent
                @component('dashboard.components.readonly-text', ['name' => 'type_name', 'model' => $productType ?? null, 'defaultValue' => $productCategory->name ?? __('Without category')]){{ __('Type') }}@endcomponent
                @component('dashboard.components.' . (isset($productType) ? 'readonly-text' : 'input-box'), ['name' => 'sku', 'model' => $productType ?? null]){{ __('SKU') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'name', 'model' => $productType ?? null]){{ __('Name') }}@endcomponent
                @component('dashboard.components.textarea', ['name' => 'description', 'model' => $productType ?? null]){{ __('Description') }}@endcomponent
                @component('dashboard.components.input-box', ['name' => 'picture', 'model' => $productType ?? null]){{ __('Picture') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'active', 'model' => $productType ?? null, 'default' => true]){{ __('Active') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'imposes_pre_invoice_negotiation', 'model' => $productType ?? null]){{ __('Impose Pre Invoice negotiation') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'on_sale', 'model' => $productType ?? null, 'default' => true]){{ __('On Sale') }}@endcomponent
                @component('dashboard.components.check-box', ['name' => 'appears_in_listing', 'model' => $productType ?? null, 'default' => true]){{ __('Appears in lists') }}@endcomponent
            </div>
            <div class="tab-pane fade @if($activeTab === 'product-settings') show active @endif" id="product-settings"
                 role="tabpanel"
                 aria-labelledby="product-settings-tab">
                @component('dashboard.components.select', ['name' => 'complex_settings[configurable_attributes][]', 'model' => $productType->complex_settings ?? null, 'modelKey' => 'configurable_attributes', 'multiple' => true])
                    @slot('options')
                        @foreach($configurableAttributes as $configurableAttributeValue => $configurableAttributeLabel)
                            @component('dashboard.components.option', ['value'=>$configurableAttributeValue, 'selectedValue' => old('complex_settings.configurable_attributes', $productType->complex_settings['configurable_attributes'] ?? [])]){{ $configurableAttributeLabel }}@endcomponent
                        @endforeach
                    @endslot
                    {{ __('Configurable Attributes') }}
                @endcomponent
            </div>
            <div class="tab-pane fade @if($activeTab === 'product-related-products') show active @endif"
                 id="product-related-products"
                 role="tabpanel"
                 aria-labelledby="product-settings-tab">
                <table id="related-products-table" class="table table-striped" style="width: 100%">
                    <thead>
                    <tr>
                        <td><i class="fa fa-square-o"></i></td>
                        <td>{{ __('Default') }}</td>
                        <td>{{ __('ID') }}</td>
                        <td>{{ __('Sku') }}</td>
                        <td>{{ __('Name') }}</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categoryProducts as $categoryProduct)
                        <tr>
                            <td>
                                <input type="checkbox" name="related_products[]" value="{{ $categoryProduct->id }}">
                            </td>
                            <td>
                                <input type="radio" name="complex_settings[default_product]"
                                       value="{{ $categoryProduct->id }}">
                            </td>
                            <td>{{ $categoryProduct->id }}</td>
                            <td>{{ $categoryProduct->sku }}</td>
                            <td>{{ $categoryProduct->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
            var lastSelectedRadio = '';
            var table = jQuery('#related-products-table').DataTable({
                columnDefs: [
                    {targets: [0, 1], searchable: false, orderable: false},
                ],
                "order": [[2, "asc"]],
                "fnDrawCallback": function (oSettings) {
                    lastSelectedRadio = function () {
                    };
                },
            });

            table.$('input[type=radio]').on('click', function (event) {
                var $me = $(this);
                var $checkbox = $me.closest('tr').find('input[type=checkbox');
                if(($checkbox.length === 0) || ($checkbox.prop('checked') === false)){
                    event.preventDefault();
                    return;
                }
                $checkbox = null;
                var v = $me.val();
                table.$('input[type=radio]').each(function () {
                    var $this = $(this);
                    $this.prop('checked', $this.val() === v);
                    $this = null;
                });
                $me = null;
            });

            table.$('input[type=checkbox]').on('click', function (event) {
                var $me = $(this);
                var $radio = $me.closest('tr').find('input[type=radio');
                var old = $radio.prop('checked');
                $radio.prop('checked', $me.prop('checked') && old);
                if($radio.prop('checked') !== old){
                    $radio.change();
                }
                $radio = null;
                $me = null;
            });

            $('#product-type-form').on('submit', function (event) {
                table.destroy();
            })
        })(jQuery);
    </script>
@endpush
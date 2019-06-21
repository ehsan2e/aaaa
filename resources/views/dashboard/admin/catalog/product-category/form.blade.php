@csrf

@component('dashboard.components.select', ['name' => 'parent_id', 'model' => $productCategory ?? null, 'autofocus' => true])
    @slot('options')
        <option value="">{{ __('Without parent') }}</option>
        @foreach($productCategories as $category)
            @component('dashboard.components.option', ['value'=>$category->id, 'selectedValue' => old('parent_id', $productCategory->parent_id ?? '')]){{ $category->name }}@endcomponent
        @endforeach
    @endslot
    {{ __('Parent Category') }}
@endcomponent
@component('dashboard.components.input-box', ['name' => 'code', 'model' => $productCategory ?? null]){{ __('Code') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'name', 'model' => $productCategory ?? null, 'required' => true]){{ __('Name') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $productCategory ?? null]){{ __('Active') }}@endcomponent
<hr class="my-5">
<h5>{{ __('Custom Attributes') }}</h5>
@error('custom_attributes')
<div class="alert alert-danger" role="alert">{{ $message }}</div>
@enderror
<attribute-set-component :attributes="{{ json_encode(old('custom_attributes', $productCategory->custom_attributes ?? [])) }}"
                         :languages="{{ json_encode($languages) }}"
                         :errors="{{ $errors->isEmpty() ? '{}' : $errors->toJson() }}"
></attribute-set-component>
<hr class="my-5">

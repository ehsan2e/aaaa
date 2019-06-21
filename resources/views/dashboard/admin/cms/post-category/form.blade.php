@csrf

@if(count($languages) === 1)
    <input type="hidden" name="language" value="{{ key($languages) }}">
@else
    @component('dashboard.components.select', ['name' => 'language', 'model' => $postCategory ?? null, 'items' => $languages, 'default' => $defaultLanguage, 'autofocus' => true]){{ __('Language') }}@endcomponent
@endif
@component('dashboard.components.select', ['name' => 'parent_id', 'model' => $postCategory ?? null])
    @slot('options')
        <option value="">{{ __('Without parent') }}</option>
        @foreach($preparedPostCategories as $languageGroup => $preparedPostCategoryList)
            <optgroup label="{{ $languageGroup }}" data-language="{{ $languageGroup }}">
                @foreach($preparedPostCategoryList as $item)
                    @component('dashboard.components.option', ['value'=>$item->id, 'selectedValue' => old('parent_id', $postCategory->parent_id ?? '')]){{ $item->name }}@endcomponent
                @endforeach
            </optgroup>
        @endforeach
    @endslot
    {{ __('Parent Category') }}
@endcomponent
@component('dashboard.components.input-box', ['name' => 'code', 'model' => $postCategory ?? null, 'required' => true]){{ __('Code') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'name', 'model' => $postCategory ?? null, 'required' => true]){{ __('Name') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'picture', 'model' => $postCategory ?? null]){{ __('Picture') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $postCategory ?? null]){{ __('Active') }}@endcomponent
<hr class="my-5">
<h5>{{ __('SEO settings') }}</h5>
@component('dashboard.components.input-box', ['name' => 'seo_link', 'model' => $postCategory->url ?? null, 'modelKey' => 'path']){{ __('Seo Link') }}@endcomponent
@include('dashboard.partials.seo-form', ['seoConfig' => $postCategory->seoConfig ?? null])
@csrf

@if(count($languages) === 1)
    <input type="hidden" name="language" value="{{ key($languages) }}">
@else
    @component('dashboard.components.select', ['name' => 'language', 'model' => $post ?? null, 'items' => $languages, 'default' => $defaultLanguage, 'autofocus' => true]){{ __('Language') }}@endcomponent
@endif
@component('dashboard.components.select', ['name' => 'category_id', 'model' => $post ?? null])
    @slot('options')
        <option value="">{{ __('Without category') }}</option>
        @foreach($preparedPostCategories as $languageGroup => $preparedPostCategoryList)
            <optgroup label="{{ $languageGroup }}" data-language="{{ $languageGroup }}">
                @foreach($preparedPostCategoryList as $item)
                    @component('dashboard.components.option', ['value'=>$item->id, 'selectedValue' => old('category_id', $post->category_id ?? '')]){{ $item->name }}@endcomponent
                @endforeach
            </optgroup>
        @endforeach
    @endslot
    {{ __('Category') }}
@endcomponent
@component('dashboard.components.input-box', ['name' => 'code', 'model' => $post ?? null, 'required' => true]){{ __('Code') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'title', 'model' => $post ?? null, 'required' => true]){{ __('Title') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'picture', 'model' => $post ?? null]){{ __('Picture') }}@endcomponent
@component('dashboard.components.textarea', ['name' => 'excerpt', 'model' => $post ?? null]){{ __('Excerpt') }}@endcomponent
@component('dashboard.components.textarea', ['name' => 'content', 'model' => $post ?? null]){{ __('Content') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $post ?? null]){{ __('Active') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'disclose_author', 'model' => $post ?? null, 'default' => 'true']){{ __('Disclose Author') }}@endcomponent
<hr class="my-5">
<h5>{{ __('SEO settings') }}</h5>
@component('dashboard.components.input-box', ['name' => 'seo_link', 'model' => $post->url ?? null, 'modelKey' => 'path']){{ __('Seo Link') }}@endcomponent
@include('dashboard.partials.seo-form', ['seoConfig' => $post->seoConfig ?? null])
@push('before-body-ends')
    <script>
        jQuery(function(){
            initWYSIWYG('textarea#excerpt');
            initWYSIWYG('textarea#content');
        });
    </script>
@endpush
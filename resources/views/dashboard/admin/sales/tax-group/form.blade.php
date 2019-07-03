@csrf
@component('dashboard.components.input-box', ['name' => 'name', 'model' => $taxGroup ?? null, 'required' => true, 'autofocus' => true]){{ __('Name') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'amount', 'model' => $taxGroup ?? null, 'required' => true]){{ __('Amount') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $taxGroup ?? null, 'default' => true]){{ __('Active') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'is_percentage', 'model' => $taxGroup ?? null, 'default' => true]){{ __('Is Percentage') }}@endcomponent
@foreach($languages as $code => $display)
    @component('dashboard.components.input-box', ['name' => 'name_translations['.$code.']', 'model' => $taxGroup->name_translations ?? null, 'modelKey' => $code]){{ __('Name :language', ['language' => $display]) }}@endcomponent
@endforeach


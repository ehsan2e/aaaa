@csrf
@component('dashboard.components.select', ['name' => 'type', 'model' => $ticketCategory ?? null])
    @slot('options')
        @foreach($types as $value => $label)
            @component('dashboard.components.option', ['value'=>$value, 'selectedValue' => old('type', $ticketCategory->type ?? \App\TicketCategory::TYPE_INCOMING)]){{ $label }}@endcomponent
        @endforeach
    @endslot
    {{ __('Category Type') }}
@endcomponent
@component('dashboard.components.input-box', ['name' => 'title', 'model' => $ticketCategory ?? null, 'required' => true, 'autofocus' => true]){{ __('Title') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $ticketCategory ?? null, 'default' => true]){{ __('Active') }}@endcomponent
@foreach($languages as $code => $display)
@component('dashboard.components.input-box', ['name' => 'title_translations['.$code.']', 'model' => $ticketCategory->title_translations ?? null, 'modelKey' => $code]){{ __('Title :language', ['language' => $display]) }}@endcomponent
@endforeach


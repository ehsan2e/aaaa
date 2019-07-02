@csrf
@component('dashboard.components.select', ['name' => 'category_id'])
    @slot('options')
        @foreach($ticketCategories as $ticketCategory)
            @component('dashboard.components.option', ['value'=>$ticketCategory->id, 'selectedValue' => old('category_id', '')]){{ \NovaVoip\translateEntity($ticketCategory, 'title') }}@endcomponent
        @endforeach
    @endslot
    {{ __('Category') }}
@endcomponent
@component('dashboard.components.select', ['name' => 'urgency', 'items' => $urgencies, 'defaultValue' => \App\Ticket::URGENCY_NORMAL])
    {{ __('Urgency') }}
@endcomponent
@component('dashboard.components.input-box', ['name' => 'subject', 'required' => true, 'autofocus' => true]){{ __('Subject') }}@endcomponent
@component('dashboard.partials.ticketing.form', ['currentFiles' => $currentFiles ?? []]) @endcomponent

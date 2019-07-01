@csrf
@component('dashboard.components.input-box', ['name' => 'name', 'model' => $client ?? null, 'required' => true, 'autofocus' => true]){{ __('Name') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $client ?? null]){{ __('Active') }}@endcomponent

@unless($client->user_id ?? false)
    <hr class="my-5">
    <h5>{{ __('Client credentials') }}</h5>
    @component('dashboard.components.check-box', ['name' => 'create_account']){{ __('I want to create an account for the client') }}@endcomponent
    @component('dashboard.components.input-box', ['name' => 'email', 'textType' => 'email']){{ __('Email') }}@endcomponent
    @component('dashboard.components.password', ['name' => 'password', 'withConfirmation' => true]){{ __('Password') }}@endcomponent
@endif

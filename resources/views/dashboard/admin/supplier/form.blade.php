@csrf
@component('dashboard.components.input-box', ['name' => 'name', 'model' => $supplier ?? null, 'required' => true, 'autofocus' => true]){{ __('Name') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $supplier ?? null]){{ __('Active') }}@endcomponent

@unless($supplier->user_id ?? false)
    <hr class="my-5">
    <h5>{{ __('Supplier credentials') }}</h5>
    @component('dashboard.components.check-box', ['name' => 'create_account']){{ __('I want to create an account for the suppliers') }}@endcomponent
    @component('dashboard.components.input-box', ['name' => 'email', 'textType' => 'email']){{ __('Email') }}@endcomponent
    @component('dashboard.components.password', ['name' => 'password', 'withConfirmation' => true]){{ __('Password') }}@endcomponent
@endif

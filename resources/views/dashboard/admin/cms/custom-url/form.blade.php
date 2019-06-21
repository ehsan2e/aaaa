@csrf

@component('dashboard.components.input-box', ['name' => 'path', 'model' => $customUrl ?? null, 'required' => true]){{ __('Path') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'redirect_url', 'model' => $customUrl ?? null]){{ __('Redirect Url') }}@endcomponent
@component('dashboard.components.select', ['name' => 'redirect_status', 'model' => $customUrl ?? null, 'items' => $redirectStatuses, 'empty' =>__('Select status if you want to redirect')]){{ __('Redirect Status') }}@endcomponent
@component('dashboard.components.select', ['name' => 'handler', 'model' => $customUrl ?? null, 'items' => $handlers, 'empty' => __('Select handler if you do not want to redirect')]){{ __('Redirect Status') }}@endcomponent
@component('dashboard.components.textarea', ['name' => 'parameters', 'model' => $customUrl ?? null, 'valueParser'=>function($v){return empty($v) ? '' : (is_scalar($v) ? $v : json_encode($v));}]){{ __('Parameters') }}@endcomponent
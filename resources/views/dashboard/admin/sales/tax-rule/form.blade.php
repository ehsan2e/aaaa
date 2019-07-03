<h4>Tax Group: {{ \NovaVoip\translateEntity($taxGroup) }}
    <i class="fa fa-circle {{ $taxGroup->active ? 'text-success' : 'text-danger' }}" title="{{ $taxGroup->active ? __('Active') : __('Inactive') }}" data-toggle="tooltip"></i>
</h4>
<div>
    <span>{{ __('Amount:') . " {$taxGroup->amount}" }} </span>
    <div class="badge badge-secondary">{{ $taxGroup->is_percentage ? __('Percentage') : __('Fixed') }}</div>
</div>
<hr class="my-3">
@csrf
@component('dashboard.components.country-province', ['countries' => $countries, 'model' => $taxRule ?? null, 'optional' => true]) @endcomponent
@component('dashboard.components.input-box', ['name' => 'priority', 'model' => $taxRule ?? null, 'required' => true, 'defaultValue' => '100']){{ __('Priority') }}@endcomponent
@component('dashboard.components.input-box', ['name' => 'amount', 'model' => $taxRule ?? null, 'required' => true, 'defaultValue' => $taxGroup->amount]){{ __('Amount') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'active', 'model' => $taxRule ?? null, 'default' => true]){{ __('Active') }}@endcomponent
@component('dashboard.components.check-box', ['name' => 'is_percentage', 'model' => $taxRule ?? null, 'default' => $taxGroup->is_percentage]){{ __('Is Percentage') }}@endcomponent

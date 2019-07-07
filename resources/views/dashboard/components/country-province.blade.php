@php
    $optional = $optional ?? false;
    $country_code = old('country_code', $model->country_code ?? $default_country_code ?? '');
    $province_code = old('province_code', $model->province_code ?? $default_province_code ?? '');
@endphp
<div class="form-group row">
    <label for="country_code" class="col-md-4 col-form-label text-md-right">{{ __('Country') }}</label>
    <div class="col-md-6">
        <select id="country_code" name="country_code"
                class="form-control chosen-select @error('country_code') is-invalid @enderror"
        >
            @if($optional)
                @component('dashboard.components.option', ['value'=>'', 'selectedValue' => $country_code]){{ __('Select Country') }}@endcomponent
            @endif
            @foreach($countries as $country)
                @component('dashboard.components.option', ['value'=>$country->code, 'selectedValue' => $country_code]){{ \NovaVoip\translateEntity($country) }}@endcomponent
            @endforeach
        </select>
        @error('country_code')
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row">
    <label for="province_code" class="col-md-4 col-form-label text-md-right">{{ __('Province') }}</label>
    <div class="col-md-6">
        <select id="province_code" name="province_code"
                class="form-control chosen-select @error('province_code') is-invalid @enderror"
        >
            @if($optional)
                @component('dashboard.components.option', ['value'=>'', 'selectedValue' => $province_code]){{ __('Select Province') }}@endcomponent
            @endif
            @foreach($countries as $country)
                <optgroup label="{{ \NovaVoip\translateEntity($country) }}">
                    @foreach($country->provinces as $province)
                        @component('dashboard.components.option', ['value'=>$province->code, 'selectedValue' => $province_code]){{ \NovaVoip\translateEntity($province) }}@endcomponent
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error('province_code')
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
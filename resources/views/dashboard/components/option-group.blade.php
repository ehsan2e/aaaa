@php
    $multiple = $multiple ?? false;
    $oldName = str_replace(['[', ']'], ['.', ''], $name);
    $value = old($oldName, (isset($model) ? (is_array($model) ? ($model[$modelKey ?? $name] ?? ($multiple ? [] : '')) : ($model->{$modelKey ?? $name} ?? ($multiple ? [] : ''))) : null) ?? (\Illuminate\Support\Arr::exists(old(), $oldName) ? '' : ($defaultValue ?? '')));
    if(isset($valueParser)){
        $value = $valueParser($value);
    }
    $inline  = $inline ?? false;
    $type  = $multiple ? 'multi-choice' : 'single-choice';
@endphp
<div class="{{($formGroup ?? true) ? 'form-group ' : ''}}row">
    <span class="col-md-4 text-md-right">{!! $slot !!}</span>
    <div class="col-md-6">
        @if(isset($empty))
            @component('dashboard.components.option', ['name' => $name, 'value'=>'', 'selectedValue' => $value, 'inline' => $inline, 'type' => $type]){{ $empty }}@endcomponent
        @endif
        @foreach($items ?? [] as $v => $l)
            @component('dashboard.components.option', ['name' => $name, 'value'=>$v, 'selectedValue' => $value, 'inline' => $inline, 'type' => $type]){{ $l }}@endcomponent
        @endforeach
        {!! $options ?? '' !!}
        @error($oldName)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
@php
    $multiple = $multiple ?? false;
    $oldName = str_replace(['[', ']'], ['.', ''], $name);
    $value = old($oldName, (isset($model) ? (is_array($model) ? ($model[$modelKey ?? $name] ?? ($multiple ? [] : '')) : ($model->{$modelKey ?? $name} ?? ($multiple ? [] : ''))) : null) ?? (\Illuminate\Support\Arr::exists(old(), $oldName) ? '' : ($defaultValue ?? '')));
    if(isset($valueParser)){
        $value = $valueParser($value);
    }
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-6">
        <select id="{{ $name }}" name="{{ $name . ($multiple ? '[]' : '') }}" @if($multiple) multiple @endif
                class="form-control @unless($bypassChosen ?? false) chosen-select @endunless @error($oldName) is-invalid @enderror"
                autocomplete="{{ $name }}"
                @if($autofocus ?? false) autofocus @endif
                @if($required ?? false) required @endif
        >
            @if(isset($empty))
                @component('dashboard.components.option', ['value'=>'', 'selectedValue' => $value]){{ $empty }}@endcomponent
            @endif
            @foreach($items ?? [] as $v => $l)
                @component('dashboard.components.option', ['value'=>$v, 'selectedValue' => $value]){{ $l }}@endcomponent
            @endforeach
            {!! $options ?? '' !!}
        </select>
        @error($oldName)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
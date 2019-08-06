@php
    $multiple = $multiple ?? false;
    $oldName = str_replace(['[', ']'], ['.', ''], $name);

    $value = old($oldName,
        (is_null(old('_token')) || !$multiple)?
        (
            (isset($model) ? (is_array($model) ? ($model[$modelKey ?? $name] ?? ($multiple ? [] : '')) : ($model->{$modelKey ?? $name} ?? ($multiple ? [] : ''))) : null)
            ?? (\Illuminate\Support\Arr::exists(old(), $oldName) ? ($multiple? []: '') : ($defaultValue ?? ($multiple? []: '')))
        ):
        []
    );
    if(isset($valueParser)){
        $value = $valueParser($value);
    }
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-6">
        <select id="{{ $name }}" name="{{ $name . ($multiple ? '[]' : '') }}" @if($multiple) multiple @endif
        class="form-control @unless($bypassChosen ?? false) chosen-select @endunless @error($oldName) is-invalid @enderror
                @someError([$oldName . '.*']) is-invalid @endSomeError"
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
        @if($multiple)
            @for($i=0; $i<count($value ?? []); $i++)
                @error($oldName . '.' . $i)
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $i + 1 }}: {{ $message }}</strong>
                </span><br>
                @enderror
            @endfor
        @endif
    </div>
</div>
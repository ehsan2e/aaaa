@php
    $oldName = str_replace(['[', ']'], ['.', ''], $name);
    $value = old($oldName, (isset($model) ? (is_array($model) ? ($model[$modelKey ?? $name] ?? '') : ($model->{$modelKey ?? $name} ?? '')) : null) ?? (\Illuminate\Support\Arr::exists(old(), $oldName) ? '' : ($defaultValue ?? '')));
    if(isset($valueParser)){
        $value = $valueParser($value);
    }
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-8">
        <textarea type="text" id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}"
               class="form-control @error($oldName) is-invalid @enderror"
               autocomplete="{{ $name }}"
               @if($autofocus ?? false) autofocus @endif
               @if($required ?? false) required @endif
        >{{ $value }}</textarea>
        @error($oldName)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
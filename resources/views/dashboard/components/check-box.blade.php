@php
    $oldName = str_replace(['[', ']'], ['.', ''], $name);
    $checked = old($oldName, (isset($model) ? (is_array($model) ? ($model[$modelKey ?? $name] ?? false) : ($model->{$modelKey ?? $name} ?? false)) : null) ?? (is_null(old('_token')) ? ($default ?? false) : false));
    if(isset($checkedParser)){
        $value = $checkedParser($checked);
    }
@endphp
<div class="form-group row">
    <div class="col-md-6  offset-md-4">
        <div class="form-check">
            <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="{{ $value ?? 1 }}"
                   class="form-check-input @error($oldName) is-invalid @enderror"
                   @if($checked) checked @endif
                   @if($autofocus ?? false) autofocus @endif
                   @if($required ?? false) required @endif
            >
            <label for="{{ $name }}" class="form-check-label">{!! $slot !!}</label>
            @error($oldName)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>
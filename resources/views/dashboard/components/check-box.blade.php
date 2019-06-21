<div class="form-group row">
    <div class="col-md-6  offset-md-4">
        <div class="form-check">
            <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="{{ $value ?? 1 }}"
                   class="form-check-input @error($name) is-invalid @enderror"
                   @if(old($name, $model->{$name} ?? (is_null(old('_token')) ? ($default ?? false) : false))) checked @endif
                   @if($autofocus ?? false) autofocus @endif
                   @if($required ?? false) required @endif
            >
            <label for="{{ $name }}" class="form-check-label">{!! $slot !!}</label>
            @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>
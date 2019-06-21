@php
    $value = old($name, $model->{$modelKey ?? $name} ?? '');
    if(isset($valueParser)){
        $value = $valueParser($value);
    }
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-8">
        <textarea type="text" id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}"
               class="form-control @error($name) is-invalid @enderror"
               autocomplete="{{ $name }}"
               @if($autofocus ?? false) autofocus @endif
               @if($required ?? false) required @endif
        >{{ $value }}</textarea>
        @error($name)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
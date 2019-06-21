@php
$value = old($name, $model->{$modelKey ?? $name} ?? '');
if(isset($valueParser)){
    $value = $valueParser($value);
}
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-6">
        <input type="{{ $textType ?? 'text' }}" id="{{ $name }}" name="{{ $name }}"
               class="form-control @error($name) is-invalid @enderror"
               value="{{ $value }}" autocomplete="{{ $name }}"
               @if($autofocus ?? false) autofocus @endif
               @if($required ?? false) required @endif
        >
        @error($name)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
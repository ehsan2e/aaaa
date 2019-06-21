@php
    $value = old($name, $model->{$modelKey ?? $name} ?? '');
    if(isset($valueParser)){
        $value = $valueParser($value);
    }
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-6">
        <select id="{{ $name }}" name="{{ $name }}"
                class="form-control @unless($bypassChosen ?? false) chosen-select @endunless @error($name) is-invalid @enderror"
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
        @error($name)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{!! $slot !!}</label>
    <div class="col-md-6">
        <input type="password" id="{{ $name }}" name="{{ $name }}"
               class="form-control @error($oldName) is-invalid @enderror"
               @if($autofocus ?? false) autofocus @endif
               @if($required ?? false) required @endif
        >
        @error($oldName)
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
@if($withConfirmation ?? false)
@component('dashboard.components.password', ['name' => $name .'_confirmation', 'required' => $required ?? false]){!! $cofirmationLabel ?? __('Password Confirmation') !!}@endcomponent
@endif
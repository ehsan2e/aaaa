@php $functionName = 'submit' . \Illuminate\Support\Str::studly($formId) @endphp
<button class="btn btn-primary g-recaptcha" data-sitekey="{{ config('nova.recaptcha_site_key') }}" data-callback='{{ $functionName }}' data-badge="inline">{!! $slot ?? __('Submit') !!}</button>
@push('before-scripts')
    <script>
        window['{{$functionName}}'] =function () {
            document.getElementById('{{ $formId }}').submit()
        }
    </script>
@endpush
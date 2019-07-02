@component('dashboard.components.textarea', ['name' => 'message', 'model' => $ticket ?? null]){{ __('Message') }}@endcomponent
@component('dashboard.components.uploader', ['number' => $number ?? 1, 'bounded' => $bounded ?? true, 'currentFiles' => $currentFiles ?? []]) @endcomponent

@push('before-body-ends')
    @can(\App\Ability::COMPOSE_HTML)
        <script>
            jQuery(function () {
                initWYSIWYG('textarea#message');
            });
        </script>
    @endcan
@endpush
<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">{{ __('Pay your invoice') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $action ?? '' }}" method="post">
                @csrf
                <div class="modal-body">
                    @component('dashboard.components.select', ['name' => 'gateway', 'items' => $gateways]) {{ __('Gateway') }} @endcomponent
                    @if($canUseCredit ?? true)
                        @component('dashboard.components.check-box', ['name' => 'use_credit']){{ __('Pay from credit') }}@endcomponent
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Pay') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('before-body-ends')
    <script>
        (function ($) {
            var $paymentModal = $('#paymentModal');
            $paymentModal.on('show.bs.modal', function (event) {
                var $form = $(this).find('form');
                var $relatedTarget = $(event.relatedTarget);
                $form.attr('action', $relatedTarget.attr('href'));
                var canUseCredit = $relatedTarget.data('can-use-credit') === 'yes';
                $form.find('#use_credit').prop('checked', false)
                    .prop('disabled', !canUseCredit)
                    .closest('.form-group')[canUseCredit ? 'show' : 'hide']();
                $form = null;
                $relatedTarget = null;
            });

            if ({{ ($initiate ?? false) ? 'true' : 'false' }}) {
                $paymentModal.modal('show');
            }
        })(jQuery);
    </script>
@endpush
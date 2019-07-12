{{ $invoice->status_caption }}
@if($invoice->paid_at) <span class="badge badge-success">{{ __('Paid') }}</span> @endif
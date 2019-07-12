@if($order->needs_negotiation)
    {{ __('Waiting for interview') }}
@elseif($order->can_be_invoiced && ($order->invoices_issued_count === 0))
    {{ __('Pending invoice') }}
@elseif($order->is_cancelled))
{{ __('Cancelled') }}
@date($order->cancelled_at, 'Y-m-d H:i')
@elseif($order->is_paid))
{{ __('Paid') }}
@date($order->paid_at, 'Y-m-d H:i')
@endif
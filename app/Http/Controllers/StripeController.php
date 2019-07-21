<?php

namespace App\Http\Controllers;

use App\Payment;

class StripeController extends Controller
{
    /**
     * @param Payment $payment
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showForm(Payment $payment)
    {
        $this->authorize('view', $payment);
        return view('payment.stripe', ['payment'=>$payment, 'stripePublishableKey' => config('nova.stripe_publishable_key')]);
    }
}

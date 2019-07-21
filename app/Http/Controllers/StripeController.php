<?php

namespace App\Http\Controllers;

use App\Payment;
use App\User;
use Illuminate\Http\Request;
use NovaVoip\Helpers\PaymentVerifierTransactionCollectionGenerator;
use function NovaVoip\supervisedTransaction;

class StripeController extends Controller
{
    /**
     * @param Payment $payment
     * @return string
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function showForm(Payment $payment)
    {
        $this->authorize('view', $payment);
        return view('payment.stripe', ['payment'=>$payment, 'stripePublishableKey' => config('nova.stripe_publishable_key')]);
    }
}

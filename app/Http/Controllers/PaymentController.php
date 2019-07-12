<?php

namespace App\Http\Controllers;

use App\Payment;
use App\User;
use Illuminate\Http\Request;
use NovaVoip\Helpers\PaymentVerifierTransactionCollectionGenerator;
use function NovaVoip\supervisedTransaction;

class PaymentController extends Controller
{
    /**
     * @param Request $request
     * @param Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function callback(Request $request, Payment $payment)
    {
        $paymentVerifierTransactionCollectionGenerator = new PaymentVerifierTransactionCollectionGenerator($payment, $request->all());
        /** @var User $user */
        $user = $payment->user;
        if($user->createTransaction($paymentVerifierTransactionCollectionGenerator)){
            flash()->success(__('Congratulations your payment was successful'));
        }else{
            flash()->error(__('Sorry it seems that there was an error in your payment'));
        }

        return redirect()->route('dashboard.client.wallet');
    }

    /**
     * @param Payment $payment
     * @return string
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function forward(Payment $payment)
    {
        $this->authorize('view', $payment);
        if (!isset($payment->process_data['forward'])) {
            abort(400);
        }

        $response = supervisedTransaction(function () use ($payment) {
            $lockedPayment = Payment::query()->lockForUpdate()->find($payment->id);
            if ($lockedPayment->status !== Payment::STATUS_NEW) {
                flash()->warning(__('This payment is in progress'));
                return redirect()->route('dashboard.client.invoice.show', ['invoice' => $lockedPayment->invoice]);
            }
            $lockedPayment->status = Payment::STATUS_IN_PROGRESS;
            $lockedPayment->save();
            return view('payment.forward', $payment->process_data['forward'] ?? []);
        }, null, true, false);

        if (!$response) {
            flash()->error(__('An unknown error happened please try again later'));
            $response = redirect()->route('dashboard.client.invoice.show', ['invoice' => $payment->invoice]);
        }
        return $response;
    }
}

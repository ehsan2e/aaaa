<?php

namespace NovaVoip\Abstracts;


use App\Invoice;
use App\Payment;
use Illuminate\Support\Facades\Auth;
use NovaVoip\Exceptions\SupervisedTransactionException;
use NovaVoip\Helpers\SuperVisedTransactionExecuter;
use NovaVoip\Interfaces\iPaymentGateway;
use Symfony\Component\HttpFoundation\Response;
use function NovaVoip\supervisedTransaction;

abstract class AbstractPaymentGateway implements iPaymentGateway
{
    /**
     * @param Payment $payment
     * @return Response
     */
    abstract protected function handleInitiate(Payment $payment): Response;

    protected function paymentNotPossible()
    {
        flash()->error(__('Payment cannot be maid at the moment please try again later'));
        return back()->withInput();
    }

    /**
     * @param Invoice $invoice
     * @return Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function initiate(Invoice $invoice): Response
    {
        return supervisedTransaction(function() use ($invoice): Response {
            $payment = new Payment();
            $payment->amount = $invoice->grand_total;
            $payment->invoice()->associate($invoice);
            $payment->user_id = Auth::id();
            $payment->gateway = $this->getCode();
            $payment->information = [];
            $payment->process_data = [];
            if(!$payment->save()){
                throw new SupervisedTransactionException('Could not create payment');
            }
            $response = $this->handleInitiate($payment);
            if(!$payment->save()){
                throw new SupervisedTransactionException('Could not update payment');
            }
            return $response;
        }, new SuperVisedTransactionExecuter(function(){return $this->paymentNotPossible();}), false, false);
    }
}
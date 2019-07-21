<?php

namespace NovaVoip\PaymentGateways;


use App\Payment;
use NovaVoip\Abstracts\AbstractPaymentGateway;
use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Error\InvalidRequest;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;

class StripeGateway extends AbstractPaymentGateway
{

    /**
     * @param Payment $payment
     * @return Response
     */
    protected function handleInitiate(Payment $payment): Response
    {
        return redirect()->route('payment.stripe', compact('payment'));
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return 'stripe';
    }

    /**
     * @return null|string
     */
    public function referenceNumber(): ?string
    {
        // TODO: Implement referenceNumber() method.
    }

    /**
     * @param Payment $payment
     * @param array $requestData
     * @return bool
     */
    public function verify(Payment $payment, array $requestData = []): bool
    {
        try {
            Stripe::setApiKey(config('nova.stripe_secret_key'));
            $token = $requestData['stripeToken'] ?? '';
            $charge = Charge::create([
                'amount' => (int)ceil($payment->amount / config('nova.currency_smallest_unit')),
                'currency' => config('nova.currency_code'),
                'description' => $payment->payment_number,
                'source' => $token,
            ]);
            if (!($charge instanceof Charge)) {
                $payment->process_data = [
                    'error' => 'Not a charge object,'
                ];
                return false;
            }
            $payment->information = [
                'id' => $charge->id,
                'balance_transaction' => $charge->balance_transaction,
                'card' => $charge->source->__toArray(),
                'billing_details' => $charge->billing_details->__toArray(),
                'payment_method' => $charge->payment_method,
                'receipt_url' => $charge->receipt_url,
                'status' => $charge->status,
            ];
            $this->refernece = $charge->id ?? null;
            return $charge->status = Charge::STATUS_SUCCEEDED;
        } catch (Base $baseException) {
            $payment->process_data = [
                'exception' => $baseException->getMessage(),
                'code' => $baseException->getStripeCode(),
            ];
            return false;
        }
    }
}
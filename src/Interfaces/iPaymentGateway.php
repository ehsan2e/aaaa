<?php

namespace NovaVoip\Interfaces;


use App\Invoice;
use App\Payment;
use Symfony\Component\HttpFoundation\Response;

interface iPaymentGateway
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @param Invoice $invoice
     * @return Response
     */
    public function initiate(Invoice $invoice): Response;

    /**
     * @return null|string
     */
    public function referenceNumber(): ?string;

    /**
     * @param Payment $payment
     * @param array $requestData
     * @return bool
     */
    public function verify(Payment $payment, array $requestData=[]): bool;
}
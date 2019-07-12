<?php

namespace NovaVoip\Helpers;


use NovaVoip\Interfaces\iPaymentGateway;

class PaymentGatewayResolver
{
    protected $gateways = [];

    public function all(): array
    {
        return $this->gateways;
    }

    /**
     * @param string $code
     * @return iPaymentGateway|null
     */
    public function label(string $code): ?string
    {
        return $this->gateways[$code]['label'] ?? null;
    }

    /**
     * @param string $code
     * @param iPaymentGateway $paymentGateway
     * @param string $label
     * @return PaymentGatewayResolver
     */
    public function register(string $code, iPaymentGateway $paymentGateway, string $label): PaymentGatewayResolver
    {
        $this->gateways[$code] = compact('paymentGateway', 'label');
        return $this;
    }

    /**
     * @param string $code
     * @return iPaymentGateway|null
     */
    public function resolve(string $code): ?iPaymentGateway
    {
        return $this->gateways[$code]['paymentGateway'] ?? null;
    }
}
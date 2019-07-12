<?php

namespace NovaVoip\PaymentGateways;


use App\Payment;
use NovaVoip\Abstracts\AbstractPaymentGateway;
use Symfony\Component\HttpFoundation\Response;

class FakeGateway extends AbstractPaymentGateway
{
    protected $refernece;

    protected function curl(string $endpoint, array $data=[])
    {
        $ch = curl_init(route('fake-payment.' . $endpoint));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);

    }

    /**
     * @param Payment $payment
     * @return Response
     */
    protected function handleInitiate(Payment $payment): Response
    {
        $data = [
            'amount' => $payment->amount,
            'id' => $payment->id,
            'return_url' => route('payment.callback', compact('payment')),
        ];

        $token = $this->curl('request', $data)->token;
        $payment->process_data = array_merge_recursive($payment->process_data, [
            'forward' => [
                'method' => 'post',
                'action' => route('fake-payment.landing'),
                'params' => [
                    'token' => $token,
                ],
            ],
        ]);
        return redirect()->route('payment.forward', compact('payment'));
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return 'fake';
    }

    /**
     * @return null|string
     */
    public function referenceNumber(): ?string
    {
        return $this->refernece;
    }

    /**
     * @param Payment $payment
     * @param array $requestData
     * @return bool
     */
    public function verify(Payment $payment, array $requestData = []): bool
    {
        $payment->process_data = array_merge_recursive($payment->process_data, [
            'request_data' => $requestData,
        ]);
        $result = $this->curl('verify', ['r' => $requestData['r'] ?? '']);
        $payment->process_data = array_merge_recursive($payment->process_data, [
            'result' => $result,
        ]);
        $this->refernece = $result->reference ?? null;
        return ($result->paid ?? false) && (($result->id ?? 0) == $payment->id) && (($result->amount ?? 0.00) == $payment->amount);
    }
}
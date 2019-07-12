<?php

namespace NovaVoip\Helpers;


use App\Invoice;
use App\Payment;
use App\Transaction as TransactionModel;
use Carbon\Carbon;
use NovaVoip\Interfaces\iTransactionCollection;
use NovaVoip\Interfaces\iTransactionCollectionGenerator;

class PaymentVerifierTransactionCollectionGenerator implements iTransactionCollectionGenerator
{

    protected $payment;
    protected $requestData;

    public function __construct(Payment $payment, array $requestData=[])
    {
        $this->payment = $payment;
        $this->requestData =$requestData;
    }

    /**
     * @param float|null $availableBalance
     * @return iTransactionCollection|null
     * @throws \Exception
     */
    public function generate(?float $availableBalance): ?iTransactionCollection
    {
        /** @var Payment $lockedPayment */
        $lockedPayment = Payment::query()->lockForUpdate()->find($this->payment->id);
        /** @var Invoice $lockedInvoice */
        $lockedInvoice = $lockedPayment->invoice()->lockForUpdate()->find($lockedPayment->invoice_id);

        if(!in_array($lockedPayment->status, [Payment::STATUS_NEW, Payment::STATUS_IN_PROGRESS])){
            return null;
        }

        if($lockedInvoice->payment_id == $lockedPayment->id){
            $lockedPayment->status = Payment::STATUS_REJECTED;
            $lockedPayment->save();
            return null;
        }

        $lockedPayment->status = Payment::STATUS_VERIFYING;
        $lockedPayment->save();

        if(!$lockedPayment->verify($this->requestData)){
            $lockedPayment->status = Payment::STATUS_FAILED;
            $lockedPayment->save();
            return null;
        }

        $lockedPayment->status = Payment::STATUS_SUCCEED;
        if(!$lockedPayment->save()){
            return null;
        }

        $lockedInvoice->payment()->associate($lockedPayment);
        $lockedInvoice->paid_at = Carbon::now();
        $lockedInvoice->status = $lockedInvoice->resolveProcessor()->process($lockedInvoice);
        $lockedInvoice->save();

        $transactionCollection = new TransactionCollection();
        $transaction = new Transaction($lockedPayment->amount, 'Bank transfer' . ($lockedPayment->reference_number ? " ({$lockedPayment->reference_number})" : ''), TransactionModel::TYPE_BANK_TRANSFER, $lockedPayment);
        $transactionCollection->add($transaction);
        return $transactionCollection;
    }
}
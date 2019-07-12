<?php

namespace NovaVoip\InvoiceProcessor;


use App\Invoice;
use NovaVoip\Interfaces\iInvoiceProcessor;

class Manual implements iInvoiceProcessor
{

    /**
     * @param Invoice $invoice
     * @return int
     */
    public function process(Invoice $invoice): int
    {
        return Invoice::STATUS_PENDING_TO_BE_PROCESSED;
    }
}
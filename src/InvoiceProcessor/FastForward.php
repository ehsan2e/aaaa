<?php

namespace NovaVoip\InvoiceProcessor;


use App\Invoice;
use NovaVoip\Interfaces\iInvoiceProcessor;

class FastForward implements iInvoiceProcessor
{
    /**
     * @param Invoice $invoice
     * @return int
     */
    public function process(Invoice $invoice): int
    {
        return Invoice::STATUS_PROCESSED;
    }
}
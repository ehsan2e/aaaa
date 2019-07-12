<?php

namespace NovaVoip\Interfaces;


use App\Invoice;

interface iInvoiceProcessor
{
    /**
     * @param Invoice $invoice
     * @return int
     */
    public function process(Invoice $invoice): int;
}
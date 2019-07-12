<?php

namespace NovaVoip\Helpers;


use NovaVoip\Interfaces\iTransaction;
use NovaVoip\Interfaces\iTransactionCollection;
use NovaVoip\Interfaces\iTransactionCollectionGenerator;

class SimpleTransactionCollectionGenerator implements iTransactionCollectionGenerator
{
    protected $transactionCollection;

    public function __construct()
    {
        $this->transactionCollection = new TransactionCollection();
    }

    public function add(iTransaction $transaction): SimpleTransactionCollectionGenerator
    {
        $this->transactionCollection->add($transaction);
        return $this;
    }
    /**
     * @param float|null $availableBalance
     * @return iTransactionCollection|null
     */
    public function generate(?float $availableBalance): ?iTransactionCollection
    {
        return $this->transactionCollection;
    }
}
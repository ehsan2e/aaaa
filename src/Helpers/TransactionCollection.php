<?php

namespace NovaVoip\Helpers;


use Generator;
use NovaVoip\Interfaces\iTransaction;
use NovaVoip\Interfaces\iTransactionCollection;

class TransactionCollection implements iTransactionCollection
{
    /**
     * @var array
     */
    protected $_transactions = [];

    public function add(iTransaction $transaction): TransactionCollection
    {
        $this->_transactions[] = $transaction;
        return $this;
    }

    /**
     * @return Generator
     */
    public function transactions(): Generator
    {
        foreach ($this->_transactions as $transaction) {
            yield $transaction;
        }
    }
}
<?php

namespace NovaVoip\Interfaces;


interface iTransactionCollectionGenerator
{
    /**
     * @param float|null $availableBalance
     * @return iTransactionCollection|null
     */
    public function generate(?float $availableBalance): ?iTransactionCollection;
}
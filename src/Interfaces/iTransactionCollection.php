<?php

namespace NovaVoip\Interfaces;


use Generator;

interface iTransactionCollection
{
    /**
     * @return Generator
     */
    public function transactions(): Generator;
}
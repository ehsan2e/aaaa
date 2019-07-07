<?php

namespace NovaVoip\Traits;


/**
 * Trait CalculatesTax
 * @package NovaVoip\Traits
 *
 * @property float amount
 * @property bool is_percentage
 */
trait CalculatesTax
{
    /**
     * @param float $price
     * @return float
     */
    public function calculate(float $price): float
    {
        return $this->is_percentage ? ($this->amount * $price / 100) :  $this->amount;
    }
}
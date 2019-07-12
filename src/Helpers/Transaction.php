<?php

namespace NovaVoip\Helpers;


use App\User;
use Illuminate\Database\Eloquent\Model;
use NovaVoip\Interfaces\iTransaction;

class Transaction implements iTransaction
{

    protected $_amount;
    protected $_description;
    protected $_initiator;
    protected $_reason;
    protected $_type;

    public function __construct(float $amount, string $description=null, int $type=null, Model $reason=null, User $initiator=null)
    {
        $this->_amount = $amount;
        $this->_description = $description;
        $this->_type= $type;
        $this->_reason = $reason;
        $this->_initiator = $initiator;
    }

    /**
     * @return float
     */
    public function amount(): float
    {
        return $this->_amount;
    }

    /**
     * @return null|string
     */
    public function description(): ?string
    {
        return $this->_description;
    }

    /**
     * @return User|null
     */
    public function initiator(): ?User
    {
        return $this->_initiator;
    }

    /**
     * @return Model|null
     */
    public function reason(): ?Model
    {
       return $this->_reason;
    }

    /**
     * @return int|null
     */
    public function type(): ?int
    {
        return $this->_type;
    }

    /**
     * @param float $amount
     * @return Transaction
     */
    public function setAmount(float $amount): Transaction
    {
        $this->_amount = $amount;
        return $this;
    }

    /**
     * @param string $description
     * @return Transaction
     */
    public function setDescription(string $description): Transaction
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * @param User $initiator
     * @return Transaction
     */
    public function setInitiator(User $initiator): Transaction
    {
        $this->_initiator = $initiator;
        return $this;
    }

    /**
     * @param Model $reason
     * @return Transaction
     */
    public function setReason(Model $reason): Transaction
    {
        $this->_reason = $reason;
        return $this;
    }

    /**
     * @param int $type
     * @return Transaction
     */
    public function setType(int $type): Transaction
    {
        $this->_type = $type;
        return $this;
    }
}
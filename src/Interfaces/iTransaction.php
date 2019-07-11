<?php

namespace NovaVoip\Interfaces;


use App\User;
use Illuminate\Database\Eloquent\Model;

interface iTransaction
{
    /**
     * @return float
     */
    public function amount(): float;

    /**
     * @return null|string
     */
    public function description(): ?string;

    /**
     * @return User|null
     */
    public function initiator(): ?User;

    /**
     * @return Model|null
     */
    public function reason(): ?Model;

    /**
     * @return int|null
     */
    public function type(): ?int;

}
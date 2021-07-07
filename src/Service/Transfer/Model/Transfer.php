<?php

namespace App\Service\Transfer\Model;

use App\Entity\Bank\Account;
use App\Entity\User;

class Transfer
{
    private User $user;

    private Account $from;

    private Account $to;

    private float $amount;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Transfer
    {
        $this->user = $user;
        return $this;
    }

    public function getFrom(): Account
    {
        return $this->from;
    }

    public function setFrom(Account $from): Transfer
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): Account
    {
        return $this->to;
    }

    public function setTo(Account $to): Transfer
    {
        $this->to = $to;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): Transfer
    {
        $this->amount = $amount;
        return $this;
    }

    public function addAmount(float $amount): Transfer
    {
        $this->amount += $amount;
        return $this;
    }
}
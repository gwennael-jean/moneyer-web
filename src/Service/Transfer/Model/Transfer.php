<?php

namespace App\Service\Transfer\Model;

use App\Entity\Bank\Account;
use App\Entity\User;

class Transfer
{
    private Account|User $from;

    private Account $to;

    private float $amount;

    public function __construct(Account|User $from, Account $to, float $amount = 0)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getFromType(): string
    {
        return $this->from instanceof Account ? "Account" : "User";
    }

    /**
     * @return Account|User
     */
    public function getFrom(): Account|User
    {
        return $this->from;
    }

    /**
     * @param Account|User $from
     * @return Transfer
     */
    public function setFrom(Account|User $from): Transfer
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return Account
     */
    public function getTo(): Account
    {
        return $this->to;
    }

    /**
     * @param Account $to
     * @return Transfer
     */
    public function setTo(Account $to): Transfer
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return float|int
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float|int $amount
     * @return Transfer
     */
    public function setAmount(float $amount): Transfer
    {
        $this->amount = $amount;
        return $this;
    }
}

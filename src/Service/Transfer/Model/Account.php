<?php

namespace App\Service\Transfer\Model;

use App\Entity\Bank\Account as AccountEntity;

class Account
{
    public float $totalResources;

    public float $totalCharges;

    public function __construct(AccountEntity $account)
    {
        $this->totalResources = $account->getTotalResources();
        $this->totalCharges = $account->getTotalCharges();
    }

    public function getTotal(): float
    {
        return $this->totalResources - $this->totalCharges;
    }

    public function addResource(float $value)
    {
        $this->totalResources += $value;
    }

    public function addCharge(float $value)
    {
        $this->totalCharges += $value;
    }
}
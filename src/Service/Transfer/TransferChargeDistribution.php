<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Charge;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

abstract class TransferChargeDistribution
{
    protected ArrayCollection $creditedAccounts;

    protected WeakMap $creditedAccountsDto;

    protected ArrayCollection $debitedAccounts;

    protected WeakMap $debitedAccountsDto;

    public function setCreditedAccounts(ArrayCollection $accounts, WeakMap $dto): void
    {
        $this->creditedAccounts = $accounts;
        $this->creditedAccountsDto = $dto;
    }

    public function setDebitedAccounts(ArrayCollection $accounts, WeakMap $dto): void
    {
        $this->debitedAccounts = $accounts;
        $this->debitedAccountsDto = $dto;
    }

    public abstract function getType(): string;

    public abstract function execute(Charge $charge, ArrayCollection $transfers): void;
}
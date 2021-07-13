<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferChargeDistribution\AccountWeakMap;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

abstract class TransferChargeDistribution
{
    private AccountWeakMap $accountWeakMap;

    /**
     * @return AccountWeakMap
     */
    public function getAccountWeakMap(): AccountWeakMap
    {
        return $this->accountWeakMap;
    }

    public function setAccountWeakMap(AccountWeakMap $accountWeakMap): self
    {
        $this->accountWeakMap = $accountWeakMap;

        return $this;
    }

    public abstract function getType(): string;

    public abstract function execute(Charge $charge, ArrayCollection $transfers): void;

    protected function getTransfer(ArrayCollection $transfers, Account $from, Account $to): Transfer
    {
        $filteredTransfers = $transfers->filter(function (Transfer $transfer) use ($from, $to) {
            return $transfer->getFrom() === $from && $transfer->getTo() === $to;
        });

        if ($filteredTransfers->count() === 1) {
            $transfer = $filteredTransfers->first();
        } else {
            $transfer = $this->createTransfer($from, $to);
            $transfers->add($transfer);
        }

        return $transfer;
    }

    protected function createTransfer(Account $from, Account $to, float $amount = 0): Transfer
    {
        return (new Transfer())
            ->setUser($from->getOwner())
            ->setFrom($from)
            ->setTo($to)
            ->setAmount($amount);
    }
}
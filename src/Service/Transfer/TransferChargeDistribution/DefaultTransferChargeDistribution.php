<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferChargeDistribution;
use Doctrine\Common\Collections\ArrayCollection;

class DefaultTransferChargeDistribution extends TransferChargeDistribution
{
    public function getType(): string
    {
        return ChargeDistributionType::VIEW;
    }
    
    public function execute(Charge $charge, ArrayCollection $transfers): void
    {
        $creditedAccount = null;

        $creditedAccountsFiltered = $this->creditedAccounts->filter(function (Account $account, int $id) use ($charge) {
            $creditedAccountDto = $this->creditedAccountsDto[$account];
            return $account->getOwner() === $charge->getAccount()->getOwner()
                && $creditedAccountDto->getTotal() >= $charge->getAmount();
        });

        if (!$creditedAccountsFiltered->isEmpty()) {
            $creditedAccount = $creditedAccountsFiltered->first();
        }

        if (null !== $creditedAccount) {
            $creditedAccountDto = $this->creditedAccountsDto[$creditedAccount];
            $debitedAccountDto = $this->debitedAccountsDto[$charge->getAccount()];

            $transfer = $this->getTransfer($transfers, $creditedAccount, $charge->getAccount());
            $transfer->addAmount($charge->getAmount());

            $creditedAccountDto->addCharge($charge->getAmount());
            $debitedAccountDto->addResource($charge->getAmount());
        }
    }

    private function getTransfer(ArrayCollection $transfers, Account $from, Account $to): Transfer
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

    private function createTransfer(Account $from, Account $to, float $amount = 0): Transfer
    {
        return (new Transfer())
            ->setUser($from->getOwner())
            ->setFrom($from)
            ->setTo($to)
            ->setAmount($amount);
    }
}
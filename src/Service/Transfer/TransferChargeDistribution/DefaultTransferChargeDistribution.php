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

        $creditedAccountsFiltered = $this->getAccountWeakMap()->getCreditedAccounts()
            ->filter(function (Account $account) use ($charge) {
                $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($account);
                return $account->getOwner() === $charge->getAccount()->getOwner()
                    && $creditedAccountDto->getTotal() >= $charge->getAmount();
            });

        if (!$creditedAccountsFiltered->isEmpty()) {
            $creditedAccount = $creditedAccountsFiltered->first();
        }

        if (null !== $creditedAccount) {
            $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($creditedAccount);
            $debitedAccountDto = $this->getAccountWeakMap()->getDebitedAccountDto($charge->getAccount());

            $transfer = $this->getTransfer($transfers, $creditedAccount, $charge->getAccount());
            $transfer->addAmount($charge->getAmount());

            $creditedAccountDto->addCharge($charge->getAmount());
            $debitedAccountDto->addResource($charge->getAmount());
        }
    }
}
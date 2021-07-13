<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Entity\User;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Transfer\TransferChargeDistribution;
use Doctrine\Common\Collections\ArrayCollection;

class FiftyFiftyTransferChargeDistribution extends TransferChargeDistribution
{
    public function __construct(
        private AccountProvider $accountProvider
    )
    {
    }

    public function getType(): string
    {
        return ChargeDistributionType::FIFTY_FIFTY;
    }

    public function execute(Charge $charge, ArrayCollection $transfers): void
    {
        $users = $charge->getChargeDistribution()->getUsers();
        $users->add($charge->getAccount()->getOwner());

        $amount = $charge->getAmount() / count($users);

        /** @var User $user */
        foreach ($users as $user) {
            $creditedAccounts = $this->accountProvider->getByUser($user)
                ->filter(fn (Account $account) => $account->getTotal() >= $amount);

            foreach ($creditedAccounts as $account) {
                $this->getAccountWeakMap()->addCreditedAccount($account);
            }
        }

        foreach ($users as $user) {
            $creditedAccount = null;

            $creditedAccountsFiltered = $this->getAccountWeakMap()->getCreditedAccounts()
                ->filter(fn (Account $account) => $account->getOwner() === $user)
                ->filter(function (Account $account) use ($charge, $amount) {
                    $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($account);
                    return $creditedAccountDto->getTotal() >= $amount;
                });

            if (!$creditedAccountsFiltered->isEmpty()) {
                $creditedAccount = $creditedAccountsFiltered->first();
            }

            if (null !== $creditedAccount) {
                $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($creditedAccount);
                $debitedAccountDto = $this->getAccountWeakMap()->getDebitedAccountDto($charge->getAccount());

                $transfer = $this->getTransfer($transfers, $creditedAccount, $charge->getAccount());
                $transfer->addAmount($amount);

                $creditedAccountDto->addCharge($amount);
                $debitedAccountDto->addResource($amount);
            }
        }
    }
}

<?php

namespace App\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Transfer\Model\Account as AccountDto;
use WeakMap;

class TransferComputer
{
    private ArrayCollection $transferChargeDistributions;

    public function __construct()
    {
        $this->transferChargeDistributions = new ArrayCollection();
    }

    public function getTransferChargeDistribution(string $type): TransferChargeDistribution
    {
        return $this->transferChargeDistributions->get($type);
    }

    public function addTransferChargeDistribution(TransferChargeDistribution $transferChargeDistribution): void
    {
        $this->transferChargeDistributions->set($transferChargeDistribution->getType(), $transferChargeDistribution);
    }

    /**
     * @param User $user
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function computeByUser(User $user, ArrayCollection $accounts): ArrayCollection
    {
        $transfers = new ArrayCollection();

        $creditedAccounts = new ArrayCollection();
        $creditedAccountsDto = new WeakMap();
        $debitedAccounts = new ArrayCollection();
        $debitedAccountsDto = new WeakMap();

        foreach ($accounts as $account) {
            if ($account->getTotal() > 0) {
                $creditedAccounts->add($account);
                $creditedAccountsDto[$account] = $this->createAccountDto($account);
            } else {
                $debitedAccounts->add($account);
                $debitedAccountsDto[$account] = $this->createAccountDto($account);
            }
        }

        /** @var Account $debitedAccount */
        foreach ($debitedAccounts as $debitedAccount) {
            foreach ($debitedAccount->getCharges() as $charge) {
                $type = null !== $charge->getChargeDistribution()
                    ? $charge->getChargeDistribution()->getType()
                    : ChargeDistributionType::VIEW;

                $transferChargeDistribution = $this->getTransferChargeDistribution($type);

                $transferChargeDistribution->setCreditedAccounts($creditedAccounts, $creditedAccountsDto);
                $transferChargeDistribution->setDebitedAccounts($debitedAccounts, $debitedAccountsDto);
                $transferChargeDistribution->execute($charge, $transfers);
            }
        }

        return $transfers;
    }

    private function createAccountDto(Account $account): AccountDto
    {
        return new AccountDto($account->getTotalResources(), $account->getTotalCharges());
    }
}
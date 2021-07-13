<?php

namespace App\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\User;
use App\Service\Transfer\TransferChargeDistribution\AccountWeakMap;
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

        $accountWeakMap = new AccountWeakMap();

        $accountWeakMap->setAccounts($accounts);

        /** @var Account $debitedAccount */
        foreach ($accountWeakMap->getDebitedAccounts() as $debitedAccount) {
            foreach ($debitedAccount->getCharges() as $charge) {
                $type = null !== $charge->getChargeDistribution()
                    ? $charge->getChargeDistribution()->getType()
                    : ChargeDistributionType::VIEW;

                $transferChargeDistribution = $this->getTransferChargeDistribution($type);

                $transferChargeDistribution->setAccountWeakMap($accountWeakMap);
                $transferChargeDistribution->execute($charge, $transfers);
            }
        }

        return $transfers;
    }
}
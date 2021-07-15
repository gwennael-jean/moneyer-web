<?php

namespace App\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\User;
use App\Service\Transfer\TransferChargeDistribution\AccountWeakMap;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function addTransferChargeDistribution(TransferChargeDistribution $transferChargeDistribution): self
    {
        $this->transferChargeDistributions->set($transferChargeDistribution->getType(), $transferChargeDistribution);

        return $this;
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

        /** @var Account $account */
        foreach ($accountWeakMap->getAccounts() as $account) {
            foreach ($account->getCharges() as $charge) {
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

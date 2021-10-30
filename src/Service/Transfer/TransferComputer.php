<?php

namespace App\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\User;
use App\Service\Transfer\Factory\TransferCollectionFactory;
use App\Service\Transfer\Model\TransferCollection;
use Doctrine\Common\Collections\ArrayCollection;

class TransferComputer
{
    private ArrayCollection $transferChargeDistributions;

    public function __construct(
        private TransferCollectionFactory $transferCollectionFactory,
        private PotRepartitorInterface $potRepartitor,
    )
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

    public function compute(ArrayCollection $accounts, \DateTime $date, ?User $user = null): TransferCollection
    {
        $transfers = $this->transferCollectionFactory->create($accounts, $date);

        /** @var Account $account */
        foreach ($accounts as $account) {
            foreach ($account->getCharges($date, $user) as $charge) {
                $type = null !== $charge->getChargeDistribution()
                    ? $charge->getChargeDistribution()->getType()
                    : ChargeDistributionType::VIEW;

                $transferChargeDistribution = $this->getTransferChargeDistribution($type);

                $transferChargeDistribution->execute($transfers, $charge);
            }
        }

        $this->potRepartitor->repartition($transfers, $accounts);

        return $transfers;
    }
}

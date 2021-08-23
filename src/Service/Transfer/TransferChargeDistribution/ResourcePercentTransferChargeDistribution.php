<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Charge;
use App\Service\Provider\Bank\ResourceProvider;
use App\Service\Transfer\Model\ResourcePercent;
use App\Service\Transfer\Model\TransferCollection;
use App\Service\Transfer\TransferChargeDistribution;

class ResourcePercentTransferChargeDistribution extends TransferChargeDistribution
{
    public function __construct(
        private ResourceProvider $resourceProvider
    )
    {
    }

    public function getType(): string
    {
        return ChargeDistributionType::RESOURCE_PERCENT;
    }

    public function execute(TransferCollection $transfers, Charge $charge): void
    {
        $transfers->getAccountBalances()->remove($charge->getAccount(), $charge->getAmount());

        $resourcesPercent = new ResourcePercent();

        $users = $charge->getChargeDistribution()->getUsers();

        foreach ($users as $user) {
            $resourcesPercent->addResources($user, $this->resourceProvider->getByUser($user));
        }

        foreach ($users as $user) {

            $amount = round($charge->getAmount() * $resourcesPercent->getPercent($user), 2);

            if ($charge->getAccount()->getOwner() === $user) {
                $transfers->getPot()->remove($user, $charge->getAmount() - $amount);
            } else {
                $transfers->getPot()->add($user, $amount);
            }
        }
    }
}

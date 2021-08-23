<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Charge;
use App\Service\Transfer\Model\TransferCollection;
use App\Service\Transfer\TransferChargeDistribution;

class FiftyFiftyTransferChargeDistribution extends TransferChargeDistribution
{
    public function getType(): string
    {
        return ChargeDistributionType::FIFTY_FIFTY;
    }

    public function execute(TransferCollection $transfers, Charge $charge): void
    {
        $amount = round($charge->getAmount() / $charge->getChargeDistribution()->getUsers()->count(), 2);

        $transfers->getAccountBalances()->remove($charge->getAccount(), $charge->getAmount());

        foreach ($charge->getChargeDistribution()->getUsers() as $user) {
            if ($charge->getAccount()->getOwner() === $user) {
                $transfers->getPot()->remove($user, $amount);
            } else {
                $transfers->getPot()->add($user, $amount);
            }
        }
    }
}

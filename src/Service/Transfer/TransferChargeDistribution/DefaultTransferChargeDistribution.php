<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Charge;
use App\Service\Transfer\Model\TransferCollection;
use App\Service\Transfer\TransferChargeDistribution;

class DefaultTransferChargeDistribution extends TransferChargeDistribution
{
    public function getType(): string
    {
        return ChargeDistributionType::VIEW;
    }

    public function execute(TransferCollection $transfers, Charge $charge): void
    {
        $transfers->getAccountBalances()->remove($charge->getAccount(), -$charge->getAmount());
    }
}

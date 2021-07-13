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
        $creditedAccount = $this->findCreditedAccount($charge);

        if (null !== $creditedAccount) {
            $this->transferProcess($creditedAccount, $charge, $transfers, $charge->getAmount());
        }
    }
}

<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Charge;
use App\Service\Transfer\Model\TransferCollection;

abstract class TransferChargeDistribution
{
    public abstract function getType(): string;

    public abstract function execute(TransferCollection $transfers, Charge $charge): void;
}

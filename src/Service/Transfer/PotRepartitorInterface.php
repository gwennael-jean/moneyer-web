<?php

namespace App\Service\Transfer;

use App\Service\Transfer\Model\TransferCollection;
use Doctrine\Common\Collections\ArrayCollection;

interface PotRepartitorInterface
{
    public function repartition(TransferCollection $transfers, ArrayCollection $accounts): void;
}

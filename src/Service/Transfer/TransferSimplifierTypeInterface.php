<?php

namespace App\Service\Transfer;

use App\Service\Transfer\Model\Transfer;
use Doctrine\Common\Collections\ArrayCollection;

interface TransferSimplifierTypeInterface
{
    public function support(Transfer $transfer, ArrayCollection $transfers): bool;

    public function execute(Transfer $transfer, ArrayCollection $transfers): void;
}

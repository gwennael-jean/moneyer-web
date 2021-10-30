<?php

namespace App\Service\Transfer\Factory;

use App\Service\Provider\Bank\ChargeProviderInterface;
use App\Service\Provider\Bank\ResourceProviderInterface;
use App\Service\Transfer\Model\TransferCollection;
use Doctrine\Common\Collections\ArrayCollection;

class TransferCollectionFactory
{
    public function __construct(
        private ChargeProviderInterface $chargeProvider,
        private ResourceProviderInterface $resourceProvider,
    )
    {
    }

    public function create(ArrayCollection $accounts, \DateTime $date): TransferCollection
    {
        $charges = $this->chargeProvider->getByAccountsAndDate($accounts, $date);
        $resources = $this->resourceProvider->getByAccountsAndDate($accounts, $date);

        return new TransferCollection($accounts, $date, $resources, $charges);
    }
}

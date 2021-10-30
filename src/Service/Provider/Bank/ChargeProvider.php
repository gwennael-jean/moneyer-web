<?php

namespace App\Service\Provider\Bank;

use App\Repository\Bank\ChargeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class ChargeProvider implements ChargeProviderInterface
{
    private WeakMap $chargesByUser;

    public function __construct(
        private ChargeRepository $chargeRepository
    )
    {
        $this->chargesByUser = new WeakMap();
    }

    /**
     * @param ArrayCollection $accounts
     * @param \DateTime $date
     * @return ArrayCollection
     */
    public function getByAccountsAndDate(ArrayCollection $accounts, \DateTime $date): ArrayCollection
    {
        return new ArrayCollection(
            $this->chargeRepository->findUnexhaustedByAccounts($accounts)
            + $this->chargeRepository->findByAccountsAndDate($accounts, $date)
        );
    }
}

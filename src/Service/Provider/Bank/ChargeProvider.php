<?php

namespace App\Service\Provider\Bank;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Repository\Bank\ChargeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class ChargeProvider
{
    private WeakMap $chargesByUser;

    public function __construct(
        private ChargeRepository $chargeRepository
    )
    {
        $this->chargesByUser = new WeakMap();
    }

    /**
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function getByUser(User $user): ArrayCollection
    {
        if (!isset($this->chargesByUser[$user])) {
            $this->chargesByUser[$user] = $this->chargeRepository->findByUser($user);
        }

        return new ArrayCollection($this->chargesByUser[$user]);
    }

    /**
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function getByAccounts(ArrayCollection $accounts): ArrayCollection
    {
        $charges = new ArrayCollection();

        foreach ($accounts as $account) {
            foreach ($account->getCharges() as $charge) {
                $charges->add($charge);
            }
        }

        return $charges;
    }
}

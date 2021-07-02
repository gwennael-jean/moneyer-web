<?php

namespace App\Service\Provider\Bank;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Repository\Bank\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;

class ResourceProvider
{
    /**
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function getByAccounts(ArrayCollection $accounts): ArrayCollection
    {
        $resources = new ArrayCollection();

        foreach ($accounts as $account) {
            foreach ($account->getResources() as $resource) {
                $resources->add($resource);
            }
        }

        return $resources;
    }
}
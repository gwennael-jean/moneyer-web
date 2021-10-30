<?php

namespace App\Service\Provider\Bank;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Repository\Bank\ResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class ResourceProvider implements ResourceProviderInterface
{
    private WeakMap $resourcesByUser;

    public function __construct(
        private ResourceRepository $resourceRepository
    )
    {
        $this->resourcesByUser = new WeakMap();
    }

    /**
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function getByUser(User $user): ArrayCollection
    {
        if (!isset($this->resourcesByUser[$user])) {
            $this->resourcesByUser[$user] = $this->resourceRepository->findByOwner($user);
        }

        return new ArrayCollection($this->resourcesByUser[$user]);
    }

    /**
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function getByAccountsAndDate(ArrayCollection $accounts, \DateTime $date): ArrayCollection
    {
        return new ArrayCollection(
            $this->resourceRepository->findUnexhaustedByAccounts($accounts)
            + $this->resourceRepository->findByAccountsAndDate($accounts, $date)
        );
    }
}

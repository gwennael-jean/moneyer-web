<?php

namespace App\Service\Provider\Bank;

use App\Entity\User;
use App\Repository\Bank\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class AccountProvider
{
    private WeakMap $accountsByUser;

    public function __construct(
        private AccountRepository $accountRepository
    )
    {
        $this->accountsByUser = new WeakMap();
    }

    public function getByUser(User $user): ArrayCollection
    {
        if (!isset($this->accountsByUser[$user])) {
            $this->accountsByUser[$user] = $this->accountRepository->findByUser($user);
        }

        return new ArrayCollection($this->accountsByUser[$user]);
    }
}
<?php

namespace App\Service\Provider\Bank;

use App\Entity\User;
use App\Repository\Bank\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;

class AccountProvider
{
    public function __construct(
        private AccountRepository $accountRepository
    )
    {
    }

    public function getByUser(User $user): ArrayCollection
    {
        $accounts = $this->accountRepository->findByUser($user);

        return new ArrayCollection($accounts);
    }
}
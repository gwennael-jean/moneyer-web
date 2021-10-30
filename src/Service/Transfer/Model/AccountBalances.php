<?php

namespace App\Service\Transfer\Model;

use App\Entity\Bank\Account;
use App\Entity\Bank\Resource;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class AccountBalances
{
    private WeakMap $map;

    public function __construct(ArrayCollection $accounts, \DateTime $date, ArrayCollection $resources)
    {
        $this->map = new WeakMap();

        foreach ($accounts as $account) {
            $this->map[$account] = $account->getTotalResources($date);
        }
    }

    public function map(): WeakMap
    {
        return $this->map;
    }

    public function get(Account $account): ?float
    {
        return isset($this->map[$account]) ? $this->map[$account] : null;
    }

    public function add(Account $account, float $amount = 0): self
    {
        if (isset($this->map[$account])) {
            $this->map[$account] += $amount;
        }

        return $this;
    }

    public function remove(Account $account, float $amount): self
    {
        if (isset($this->map[$account])) {
            $this->map[$account] -= $amount;
        }

        return $this;
    }
}

<?php

namespace App\Service\Transfer\Model;

use App\Entity\User;
use WeakMap;

class LivingWage
{
    private WeakMap $map;

    public function __construct()
    {
        $this->map = new WeakMap();
    }

    public function map(): WeakMap
    {
        return $this->map;
    }

    public function get(User $user): ?float
    {
        return isset($this->map[$user]) ? $this->map[$user] : null;
    }

    public function add(User $user, float $amount): self
    {
        $this->init($user);

        $this->map[$user] += $amount;

        return $this;
    }

    public function remove(User $user, float $amount): self
    {
        $this->init($user);

        $this->map[$user] -= $amount;

        return $this;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->map as $amount) {
            $total += $amount;
        }

        return $total;
    }

    public function init(User $user)
    {
        if (!isset($this->map[$user])) {
            $this->map[$user] = 0;
        }
    }
}

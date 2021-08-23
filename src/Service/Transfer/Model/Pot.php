<?php

namespace App\Service\Transfer\Model;

use App\Entity\User;
use WeakMap;

class Pot
{
    private WeakMap $map;

    public function __construct()
    {
        $this->map = new WeakMap();
    }

    public function isEmpty()
    {
        foreach ($this->map as $amount) {
            if ($amount > 0) {
                return false;
            }
        }

        return true;
    }

    public function getFirstPayer(): ?User
    {
        foreach ($this->map as $user => $amount) {
            if ($amount > 0) {
                return $user;
            }
        }

        return null;
    }

    public function getFirstReceiver(): ?User
    {
        foreach ($this->map as $user => $amount) {
            if ($amount < 0) {
                return $user;
            }
        }

        return null;
    }

    public function get(User $user): ?float
    {
        return isset($this->map[$user]) ? $this->map[$user] : null;
    }

    public function add(User $user, float $amount)
    {
        $this->init($user);

        $this->map[$user] += $amount;

        return $this;
    }

    public function remove(User $user, float $amount)
    {
        $this->init($user);

        $this->map[$user] -= $amount;

        return $this;
    }

    private function init(User $user)
    {
        if (!isset($this->map[$user])) {
            $this->map[$user] = 0;
        }
    }
}

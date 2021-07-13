<?php

namespace App\Service\Transfer\TransferChargeDistribution\Model;

use App\Entity\Bank\Resource;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class ResourcePercent
{
    private float $total;

    private WeakMap $totalByUser;

    public function __construct()
    {
        $this->total = 0;
        $this->totalByUser = new WeakMap();
    }

    public function addResources(User $user, ArrayCollection $resources): self
    {
        if (!isset($this->totalByUser[$user])) {
            $this->totalByUser[$user] = 0;
        }

        $sum = array_reduce($resources->toArray(), fn ($carry, Resource $resource) => $carry += $resource->getAmount());
        $this->totalByUser[$user] += $sum;
        $this->total += $sum;

        return $this;
    }

    public function getPercent(User $user, $multiplier = false): float
    {
        return ($this->totalByUser[$user] * ($multiplier ? 100 : 1)) / $this->total;
    }
}

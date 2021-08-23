<?php

namespace App\Service\Transfer\Model;

use App\Entity\Bank\Resource;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class ResourcePercent
{
    private WeakMap $map;
    private float $total;

    public function __construct()
    {
        $this->map = new WeakMap();
        $this->total = 0;
    }

    public function addResources(User $user, ArrayCollection $resources): self
    {
        if (!isset($this->map[$user])) {
            $this->map[$user] = 0;
        }

        foreach ($resources as $resource) {
            $this->map[$user] += $resource->getAmount();
            $this->total += $resource->getAmount();
        }

        return $this;
    }

    public function getPercent(User $user): float
    {
        return $this->map[$user] / $this->total;
    }
}

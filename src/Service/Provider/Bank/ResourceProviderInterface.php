<?php

namespace App\Service\Provider\Bank;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

interface ResourceProviderInterface
{
    public function getByUser(User $user): ArrayCollection;
}

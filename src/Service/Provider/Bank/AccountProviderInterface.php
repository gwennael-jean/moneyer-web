<?php

namespace App\Service\Provider\Bank;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

interface AccountProviderInterface
{
    public function getByUser(User $user): ArrayCollection;
}

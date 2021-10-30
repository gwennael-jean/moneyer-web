<?php

namespace App\Service\Provider\Bank;

use Doctrine\Common\Collections\ArrayCollection;

interface ChargeProviderInterface
{
    public function getByAccountsAndDate(ArrayCollection $accounts, \DateTime $date): ArrayCollection;
}

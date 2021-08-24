<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Account;
use App\Service\Transfer\Model\LivingWage;
use App\Service\Transfer\Model\TransferCollection;

class LivingWageComputer
{
    /**
     * @param TransferCollection $transfers
     * @return LivingWage
     */
    public function compute(TransferCollection $transfers): LivingWage
    {
        $livingWage = new LivingWage();

        /**
         * @var Account $account
         * @var float $balance
         */
        foreach ($transfers->getAccountBalances()->map() as $account => $balance) {
            if (null !== $account->getOwner()) {
                $livingWage->add($account->getOwner(), $balance);
            }
        }

        return $livingWage;
    }
}

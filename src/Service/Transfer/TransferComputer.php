<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Service\Transfer\Model\Transfer;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Transfer\Model\Account as AccountDto;
use WeakMap;

class TransferComputer
{
    /**
     * @param User $user
     * @param ArrayCollection|Account[] $accounts
     * @return ArrayCollection
     */
    public function computeByUser(User $user, ArrayCollection $accounts): ArrayCollection
    {
        $transfers = new ArrayCollection();

        $creditedAccounts = new ArrayCollection();
        $creditedAccountsDto = new WeakMap();
        $debitedAccounts = new ArrayCollection();
        $debitedAccountsDto = new WeakMap();

        foreach ($accounts as $account) {
            if ($account->getTotal() > 0) {
                $creditedAccounts->add($account);
                $creditedAccountsDto[$account] = $this->createAccountDto($account);
            } else {
                $debitedAccounts->add($account);
                $debitedAccountsDto[$account] = $this->createAccountDto($account);
            }
        }

        /** @var Account $debitedAccount */
        foreach ($debitedAccounts as $debitedAccount) {
            $debitedAccountDto = $debitedAccountsDto[$debitedAccount];

            foreach ($debitedAccount->getCharges() as $charge) {

                $creditedAccount = null;
                $creditedAccountDto = null;

                $creditedAccountsFiltered = $creditedAccounts->filter(function (Account $account, int $id) use ($creditedAccountsDto, $charge) {
                    $creditedAccountDto = $creditedAccountsDto[$account];
                    return $account->getOwner() === $charge->getAccount()->getOwner()
                        && $creditedAccountDto->getTotal() >= $charge->getAmount();
                });

                if (!$creditedAccountsFiltered->isEmpty()) {
                    $creditedAccount = $creditedAccountsFiltered->first();
                }

                if (null !== $creditedAccount) {
                    $creditedAccountDto = $creditedAccountsDto[$creditedAccount];

                    $transfer = $this->getTransfer($transfers, $creditedAccount, $debitedAccount);
                    $transfer->addAmount($charge->getAmount());

                    $creditedAccountDto->addCharge($charge->getAmount());
                    $debitedAccountDto->addResource($charge->getAmount());
                }
            }
        }

        return $transfers;
    }

    private function getTransfer(ArrayCollection $transfers, Account $from, Account $to): Transfer
    {
        $filteredTransfers = $transfers->filter(function (Transfer $transfer) use ($from, $to) {
            return $transfer->getFrom() === $from && $transfer->getTo() === $to;
        });

        if ($filteredTransfers->count() === 1) {
            $transfer = $filteredTransfers->first();
        } else {
            $transfer = $this->createTransfer($from, $to);
            $transfers->add($transfer);
        }

        return $transfer;
    }

    private function createTransfer(Account $from, Account $to, float $amount = 0): Transfer
    {
        return (new Transfer())
            ->setUser($from->getOwner())
            ->setFrom($from)
            ->setTo($to)
            ->setAmount($amount);
    }

    private function createAccountDto(Account $account): AccountDto
    {
        return new AccountDto($account->getTotalResources(), $account->getTotalCharges());
    }
}
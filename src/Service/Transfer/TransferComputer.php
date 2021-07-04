<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Service\Transfer\Model\Transfer;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Transfer\Model\Account as AccountDto;

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
        $creditedAccountsDto = new ArrayCollection();
        $debitedAccounts = new ArrayCollection();
        $debitedAccountsDto = new ArrayCollection();

        foreach ($accounts as $account) {
            if ($account->getTotal() > 0) {
                $creditedAccounts->add($account);
                $creditedAccountsDto->set($account->getId(), $this->createAccountDto($account));
            } else {
                $debitedAccounts->add($account);
                $debitedAccountsDto->set($account->getId(), $this->createAccountDto($account));
            }
        }

        /** @var Account $debitedAccount */
        foreach ($debitedAccounts as $debitedAccount) {
            $debitedAccountDto = $debitedAccountsDto->get($debitedAccount->getId());

            foreach ($debitedAccount->getCharges() as $charge) {

                $creditedAccount = null;
                $creditedAccountDto = null;

                $creditedAccountsFiltered = $creditedAccounts->filter(function (Account $account, int $id) use ($creditedAccountsDto, $charge) {
                    $creditedAccountDto = $creditedAccountsDto->get($account->getId());
                    return $creditedAccountDto->getTotal() >= $charge->getAmount();
                });

                if (!$creditedAccountsFiltered->isEmpty()) {
                    $creditedAccount = $creditedAccountsFiltered->first();
                }

                if (null !== $creditedAccount) {
                    $creditedAccountDto = $creditedAccountsDto->get($creditedAccount->getId());

                    $transfer = $this->getTransfer($transfers, $user, $creditedAccount, $debitedAccount);
                    $transfer->addAmount($charge->getAmount());

                    $creditedAccountDto->addCharge($charge->getAmount());
                    $debitedAccountDto->addResource($charge->getAmount());
                }
            }
        }

        return $transfers;
    }

    private function getTransfer(ArrayCollection $transfers, User $user, Account $from, Account $to): Transfer
    {
        $filteredTransfers = $transfers->filter(function (Transfer $transfer) use ($user, $from, $to) {
            return $transfer->getUser() === $user
                && $transfer->getFrom() === $from
                && $transfer->getTo() === $to;
        });

        if ($filteredTransfers->count() === 1) {
            $transfer = $filteredTransfers->first();
        } else {
            $transfer = $this->createTransfer($user, $from, $to);
            $transfers->add($transfer);
        }

        return $transfer;
    }

    private function createTransfer(User $user, Account $from, Account $to, float $amount = 0): Transfer
    {
        return (new Transfer())
            ->setUser($user)
            ->setFrom($from)
            ->setTo($to)
            ->setAmount($amount);
    }

    private function createAccountDto(Account $account): AccountDto
    {
        return new AccountDto($account->getTotalResources(), $account->getTotalCharges());
    }
}
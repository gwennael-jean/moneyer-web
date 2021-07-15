<?php

namespace App\Service\Transfer;

use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferChargeDistribution\AccountWeakMap;
use Doctrine\Common\Collections\ArrayCollection;

abstract class TransferChargeDistribution
{
    private AccountWeakMap $accountWeakMap;

    /**
     * @return AccountWeakMap
     */
    public function getAccountWeakMap(): AccountWeakMap
    {
        return $this->accountWeakMap;
    }

    public function setAccountWeakMap(AccountWeakMap $accountWeakMap): self
    {
        $this->accountWeakMap = $accountWeakMap;

        return $this;
    }

    public abstract function getType(): string;

    public abstract function execute(Charge $charge, ArrayCollection $transfers): void;

    /**
     * Methode permettant de récupérer un compte credite
     *
     * @param Charge $charge
     * @param array $options
     * @return Account|null
     */
    protected function findCreditedAccount(Charge $charge, array $options = []): ?Account
    {
        $creditedAccountsFiltered = $this->getAccountWeakMap()->getCreditedAccounts()
            ->filter(fn (Account $account) => $this->creditedAccountfilter($account, $charge, $options));

        return !$creditedAccountsFiltered->isEmpty()
            ? $creditedAccountsFiltered->first()
            : null;
    }

    /**
     * Cette methode est appele dans findCreditedAccount() afin e filtrer les comptes pouvant supporter la charge
     *
     * @param Account $account
     * @param Charge $charge
     * @param array $options
     * @return bool
     */
    protected function creditedAccountfilter(Account $account, Charge $charge, array $options = []): bool
    {
        $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($account);

        return $account->getOwner() === $charge->getAccount()->getOwner()
            && $creditedAccountDto->getTotal() >= $charge->getAmount();
    }

    protected function transferProcess(Account $creditedAccount, Charge $charge, ArrayCollection $transfers, float $amount)
    {
        $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($creditedAccount);
        $debitedAccountDto = $this->getAccountWeakMap()->getDebitedAccountDto($charge->getAccount());

        if ($creditedAccount !== $charge->getAccount()) {
            $transfer = $this->getTransfer($transfers, $creditedAccount, $charge->getAccount());
            $transfer->addAmount($amount);
        }

        $creditedAccountDto->addCharge($amount);
        $debitedAccountDto->addResource($amount);
    }

    protected function getTransfer(ArrayCollection $transfers, Account $from, Account $to): Transfer
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

    protected function createTransfer(Account $from, Account $to, float $amount = 0): Transfer
    {
        return (new Transfer())
            ->setUser($from->getOwner())
            ->setFrom($from)
            ->setTo($to)
            ->setAmount($amount);
    }
}

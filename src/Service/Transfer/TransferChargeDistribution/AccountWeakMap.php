<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\Entity\Bank\Account;
use App\Service\Transfer\Model\Account as AccountDto;
use Doctrine\Common\Collections\ArrayCollection;
use WeakMap;

class AccountWeakMap
{
    private ArrayCollection $creditedAccounts;

    private WeakMap $creditedAccountsDto;

    private ArrayCollection $debitedAccounts;

    private WeakMap $debitedAccountsDto;

    public function __construct()
    {
        $this->creditedAccounts = new ArrayCollection();
        $this->creditedAccountsDto = new WeakMap();
        $this->debitedAccounts = new ArrayCollection();
        $this->debitedAccountsDto = new WeakMap();
    }

    public function setAccounts(ArrayCollection $accounts): self
    {
        $this
            ->setCreditedAccounts($accounts->filter(fn (Account $account) => $account->getTotal() > 0))
            ->setDebitedAccounts($accounts->filter(fn (Account $account) => $account->getTotal() < 0));

        return $this;
    }

    public function addAccount(Account $account): self
    {
        if ($account->getTotal() > 0) {
            $this->addCreditedAccount($account);
        } else if ($account->getTotal() < 0) {
            $this->addDebitedAccount($account);
        }

        return $this;
    }

    public function getCreditedAccounts(): ArrayCollection
    {
        return $this->creditedAccounts;
    }

    public function setCreditedAccounts(ArrayCollection $accounts): self
    {
        foreach ($accounts as $account) {
            $this->addCreditedAccount($account);
        }

        return $this;
    }

    public function addCreditedAccount(Account $account): self
    {
        if (!$this->creditedAccounts->contains($account)) {
            $this->creditedAccounts->add($account);
            $this->creditedAccountsDto[$account] = new AccountDto($account);
        }

        return $this;
    }

    public function getCreditedAccountDto(Account $account): ?AccountDto
    {
        return isset($this->creditedAccountsDto[$account])
            ? $this->creditedAccountsDto[$account]
            : null;
    }

    public function getDebitedAccounts(): ArrayCollection
    {
        return $this->debitedAccounts;
    }

    public function setDebitedAccounts(ArrayCollection $accounts): self
    {
        foreach ($accounts as $account) {
            $this->addDebitedAccount($account);
        }

        return $this;
    }

    public function addDebitedAccount(Account $account): self
    {
        if (!$this->debitedAccounts->contains($account)) {
            $this->debitedAccounts->add($account);
            $this->debitedAccountsDto[$account] = new AccountDto($account);
        }

        return $this;
    }

    public function getDebitedAccountDto(Account $account): ?AccountDto
    {
        return isset($this->debitedAccountsDto[$account])
            ? $this->debitedAccountsDto[$account]
            : null;
    }
}
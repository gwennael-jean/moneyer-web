<?php

namespace App\Service\Transfer\Model;

use App\Entity\Bank\Account;
use ArrayIterator;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;

class TransferCollection implements IteratorAggregate
{
    private ArrayCollection $transfers;

    private AccountBalances $accountBalances;

    private Pot $pot;

    private ArrayCollection $resources;

    private ArrayCollection $charges;

    public function __construct(ArrayCollection $accounts, \DateTime $date, ArrayCollection $resources, ArrayCollection $charges)
    {
        $this->transfers = new ArrayCollection();
        $this->pot = new Pot();
        $this->resources = $resources;
        $this->charges = $charges;
        $this->accountBalances = new AccountBalances($accounts, $date, $this->resources);
    }

    public function add(Transfer $transfer): self
    {
        $this->transfers->add($transfer);

        return $this;
    }

    /**
     * @return Transfer[]
     */
    public function toArray(): array
    {
        return $this->transfers->toArray();
    }

    public function getPot(): Pot
    {
        return $this->pot;
    }

    public function getAccountBalances(): AccountBalances
    {
        return $this->accountBalances;
    }

    public function filter(Closure $closure): ArrayCollection
    {
        return $this->transfers->filter($closure);
    }

    public function getDebitedAccount(): ?Account
    {
        foreach ($this->getAccountBalances()->map() as $account => $amount) {
            if ($amount < 0) {
                return $account;
            }
        }

        return null;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

}

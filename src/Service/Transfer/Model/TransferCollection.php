<?php

namespace App\Service\Transfer\Model;

use ArrayIterator;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;

class TransferCollection implements IteratorAggregate
{
    private ArrayCollection $transfers;

    private AccountBalances $accountBalances;

    private Pot $pot;

    public function __construct(ArrayCollection $accounts, \DateTime $date)
    {
        $this->transfers = new ArrayCollection();
        $this->pot = new Pot();
        $this->accountBalances = new AccountBalances($accounts, $date);
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

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

}

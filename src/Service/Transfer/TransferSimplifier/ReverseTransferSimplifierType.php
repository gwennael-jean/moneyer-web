<?php

namespace App\Service\Transfer\TransferSimplifier;

use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferSimplifierTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ReverseTransferSimplifierType implements TransferSimplifierTypeInterface
{
    /**
     * @var ?Transfer
     */
    private $reverseTransfer;

    public function __construct()
    {
        $this->reverseTransfer = null;
    }

    public function support(Transfer $transfer, ArrayCollection $transfers): bool
    {
        $this->reverseTransfer = $this->findReverse($transfer, $transfers);

        return null !== $this->reverseTransfer;
    }

    public function execute(Transfer $transfer, ArrayCollection $transfers): void
    {
        if ($transfer->getAmount() === $this->reverseTransfer->getAmount()) {
            $transfers->removeElement($transfer);
            $transfers->removeElement($this->reverseTransfer);
        } else {
            if ($transfer->getAmount() > $this->reverseTransfer->getAmount()) {
                $transfer->subAmount($this->reverseTransfer->getAmount());
                $transfers->removeElement($this->reverseTransfer);
            } else {
                $this->reverseTransfer->subAmount($transfer->getAmount());
                $transfers->removeElement($transfer);
            }
        }
    }

    private function findReverse(Transfer $mainTransfer, ArrayCollection $transfers): ?Transfer
    {
        $reversedTransfers = $transfers->filter(function (Transfer $transfer) use ($mainTransfer) {
            return $transfer->getFrom() === $mainTransfer->getTo() && $transfer->getTo() === $mainTransfer->getFrom();
        });

        return !$reversedTransfers->isEmpty() ? $reversedTransfers->first() : null;
    }
}

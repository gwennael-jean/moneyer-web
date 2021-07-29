<?php

namespace App\Service\Transfer\TransferSimplifier;

use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferSimplifierTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;

class SameRecipientAccountSimplifierType implements TransferSimplifierTypeInterface
{
    /**
     * @var Transfer[]
     */
    private $processedTransfers;

    /**
     * @var ?Transfer
     */
    private $reverseTransfer;

    public function __construct()
    {
        $this->processedTransfers = new ArrayCollection();
        $this->reverseTransfer = null;
    }

    public function support(Transfer $transfer, ArrayCollection $transfers): bool
    {
        $transfersFiltered = $this->findSameRecipientAccountTransfers($transfer, $transfers);
        return null !== $transfersFiltered;
    }

    public function execute(Transfer $transfer, ArrayCollection $transfers): void
    {
        $transfersFiltered = $this->findSameRecipientAccountTransfers($transfer, $transfers);

        $transfersSender = $transfersFiltered->filter(function (Transfer $t) use ($transfer) {
            return $transfer->getFrom() === $t->getFrom();
        });

        $transfersReceiver = $transfersFiltered->filter(function (Transfer $t) use ($transfer) {
            return $transfer->getTo() === $t->getFrom();
        });

        if (!$transfersReceiver->isEmpty() && !$transfersSender->isEmpty()) {

            /** @var Transfer $transferReceiver */
            $transferReceiver = $transfersReceiver->first();

            /** @var Transfer $transferSender */
            $transferSender = $transfersSender->first();

            if ($transferReceiver->getAmount() > $transfer->getAmount()) {
                $transferReceiver->subAmount($transfer->getAmount());
                $transferSender->addAmount($transfer->getAmount());

                $transfers->removeElement($transfer);

                if ($transferReceiver->getAmount() == 0) {
                    $transfers->removeElement($transferReceiver);
                }
            }
        }
    }

    private function findSameRecipientAccountTransfers(Transfer $transfer, ArrayCollection $transfers): ?ArrayCollection
    {
        /** @var Transfer[] $transfersSender */
        $transfersSender = $transfers->filter(function (Transfer $t) use ($transfer) {
            return $t !== $transfer && $transfer->getFrom() === $t->getFrom();
        });

        /** @var Transfer[] $transfersReceiver */
        $transfersReceiver = $transfers->filter(function (Transfer $t) use ($transfer) {
            return $t !== $transfer && $transfer->getTo() === $t->getFrom();
        });

        foreach ($transfersSender as $transferSender) {
            foreach ($transfersReceiver as $transferReceiver) {
                if ($transferSender->getTo() === $transferReceiver->getTo()) {
                    return new ArrayCollection([$transferSender, $transferReceiver]);
                }
            }
        }

        return null;
    }
}

<?php

namespace App\Service\Transfer;

use App\Service\Transfer\Model\Transfer;
use Doctrine\Common\Collections\ArrayCollection;

class TransferSimplifier
{
    private ArrayCollection $transferSimplifierTypes;

    public function __construct()
    {
        $this->transferSimplifierTypes = new ArrayCollection();
    }

    public function addTransferSimplifierType(TransferSimplifierTypeInterface $transferSimplifierType): self
    {
        $this->transferSimplifierTypes->add($transferSimplifierType);

        return $this;
    }

    /**
     * @param Transfer[] $transfers
     * @return ArrayCollection
     */
    public function simplify(ArrayCollection $transfers): ArrayCollection
    {
        foreach ($transfers as $transfer) {
            /** @var TransferSimplifierTypeInterface $transferSimplifierType */
            foreach ($this->transferSimplifierTypes as $transferSimplifierType) {
                if ($transfers->contains($transfer)) {
                    if ($transferSimplifierType->support($transfer, $transfers)) {
                        $transferSimplifierType->execute($transfer, $transfers);
                    }
                }
            }
        }

        return $transfers;
    }
}

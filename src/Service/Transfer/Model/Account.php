<?php

namespace App\Service\Transfer\Model;

class Account
{
    public float $totalResources;

    public float $totalCharges;

    public function __construct(float $totalResources, float $totalCharges)
    {
        $this->totalResources = $totalResources;
        $this->totalCharges = $totalCharges;
    }

    public function getTotal(): float
    {
        return $this->totalResources - $this->totalCharges;
    }

    public function addResource(float $value)
    {
        $this->totalResources += $value;
    }

    public function addCharge(float $value)
    {
        $this->totalCharges += $value;
    }
}
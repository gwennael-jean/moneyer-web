<?php

namespace App\Entity\Bank;

use App\Repository\Bank\ChargeGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChargeGroupRepository::class)
 * @ORM\Table(name="bank_charge_group")
 */
class ChargeGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\OneToOne(targetEntity=ChargeDistribution::class, cascade={"persist", "remove"})
     */
    private $chargeDistribution;

    /**
     * @ORM\OneToMany(targetEntity=Charge::class, mappedBy="chargeGroup", cascade={"persist"})
     */
    private $charges;

    public function __construct()
    {
        $this->charges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getChargeDistribution(): ?ChargeDistribution
    {
        return $this->chargeDistribution;
    }

    public function hasChargeDistribution(): bool
    {
        return null !== $this->chargeDistribution;
    }

    public function setChargeDistribution(?ChargeDistribution $chargeDistribution): self
    {
        $this->chargeDistribution = $chargeDistribution;

        return $this;
    }

    /**
     * @return Collection|Charge[]
     */
    public function getCharges(): Collection
    {
        return $this->charges;
    }

    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge
                ->setChargeGroup($this)
                ->setAccount($this->getAccount());
        }

        return $this;
    }

    public function removeCharge(Charge $charge): self
    {
        if ($this->charges->removeElement($charge)) {
            // set the owning side to null (unless already changed)
            if ($charge->getChargeGroup() === $this) {
                $charge->setChargeGroup(null);
            }
        }

        return $this;
    }

    public function getStartAt()
    {
        return $this->startAt;
    }

    public function setStartAt($startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }
}

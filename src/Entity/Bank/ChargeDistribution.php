<?php

namespace App\Entity\Bank;

use App\Entity\User;
use App\Repository\Bank\ChargeDistributionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChargeDistributionRepository::class)
 * @ORM\Table(name="bank_charge_distribution")
 */
class ChargeDistribution
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Charge::class, inversedBy="chargeDistribution", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $charge;

    /**
     * @ORM\Column(type="ChargeDistributionType")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCharge(): ?Charge
    {
        return $this->charge;
    }

    public function setCharge(Charge $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
}

<?php

namespace App\Entity\Bank;

use App\Entity\User;
use App\Repository\Bank\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @ORM\Table(name="bank_account")
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="account", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $resources;

    /**
     * @ORM\OneToMany(targetEntity=Charge::class, mappedBy="account", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $charges;

    /**
     * @ORM\OneToMany(targetEntity=AccountShare::class, mappedBy="account", orphanRemoval=true)
     */
    private $accountShares;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
        $this->charges = new ArrayCollection();
        $this->accountShares = new ArrayCollection();
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Resource[]
     */
    public function getResources(\DateTime $date): Collection
    {
        $criteria = Criteria::create();

        $criteria
            ->andWhere(Criteria::expr()->eq("month", $date));

        return $this->resources->matching($criteria);
    }

    public function hasResources(): bool
    {
        return $this->resources->count() > 0;
    }

    public function addResource(Resource $resource): self
    {
        if (!$this->resources->contains($resource)) {
            $this->resources[] = $resource;
            $resource->setAccount($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->removeElement($resource)) {
            // set the owning side to null (unless already changed)
            if ($resource->getAccount() === $this) {
                $resource->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Charge[]
     */
    public function getCharges(\DateTime $date, ?User $user = null): Collection
    {
        $criteria = Criteria::create();

        $criteria
            ->andWhere(Criteria::expr()->eq("month", $date));

        $charges = $this->charges->matching($criteria);

        if (null !== $user) {
            return $charges->filter(function (Charge $charge) use ($user) {
                return $charge->getAccount()->getCreatedBy() === $user
                    || $charge->getAccount()->getOwner() === $user
                    || (null !== $charge->getChargeDistribution() && $charge->getChargeDistribution()->getUsers()->contains($user));
            });
        }

        return $charges;
    }

    public function hasCharges(): bool
    {
        return $this->charges->count() > 0;
    }

    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge->setAccount($this);
        }

        return $this;
    }

    public function removeCharge(Charge $charge): self
    {
        if ($this->charges->removeElement($charge)) {
            // set the owning side to null (unless already changed)
            if ($charge->getAccount() === $this) {
                $charge->setAccount(null);
            }
        }

        return $this;
    }

    public function getTotalResources(\DateTime $date): float
    {
        $total = 0;

        foreach ($this->getResources($date) as $resource) {
            $total += $resource->getAmount();
        }

        return $total;
    }

    public function getTotalCharges(\DateTime $date, ?User $user = null): float
    {
        $total = 0;

        foreach ($this->getCharges($date, $user) as $charge) {
            $total += $charge->getAmount();
        }

        return $total;
    }

    public function getTotal(\DateTime $date, ?User $user = null): float
    {
        return $this->getTotalResources($date) - $this->getTotalCharges($date, $user);
    }

    /**
     * @return Collection|AccountShare[]
     */
    public function getAccountShares(): Collection
    {
        return $this->accountShares;
    }

    public function addAccountShare(AccountShare $accountShare): self
    {
        if (!$this->accountShares->contains($accountShare)) {
            $this->accountShares[] = $accountShare;
            $accountShare->setAccount($this);
        }

        return $this;
    }

    public function removeAccountShare(AccountShare $accountShare): self
    {
        if ($this->accountShares->removeElement($accountShare)) {
            // set the owning side to null (unless already changed)
            if ($accountShare->getAccount() === $this) {
                $accountShare->setAccount(null);
            }
        }

        return $this;
    }

    public function getShareUsers(): Collection
    {
        return $this->getAccountShares()->map(fn(AccountShare $accountShare) => $accountShare->getUser());
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}

<?php

namespace App\Entity\Bank;

use App\Repository\Bank\ResourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResourceRepository::class)
 * @ORM\Table(name="bank_resource")
 */
class Resource
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
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="resources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=ResourceGroup::class, inversedBy="resources")
     */
    private $resourceGroup;

    /**
     * @ORM\Column(type="MonthType", nullable=true)
     */
    private $month;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getResourceGroup(): ?ResourceGroup
    {
        return $this->resourceGroup;
    }

    public function setResourceGroup(?ResourceGroup $resourceGroup): self
    {
        $this->resourceGroup = $resourceGroup;

        return $this;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function setMonth($month): self
    {
        $this->month = $month;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf("%s (%s)", $this->getName(), $this->getAmount());
    }
}

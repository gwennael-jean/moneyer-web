<?php

namespace App\Entity\Bank;

use App\Repository\Bank\ResourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

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
    #[Serializer\Groups(["account:list"])]
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Serializer\Groups(["account:list"])]
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    #[Serializer\Groups(["account:list"])]
    private $amount;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    #[Serializer\Groups(["account:list"])]
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="resources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf("%s (%s)", $this->getName(), $this->getAmount());
    }
}

<?php

namespace App\Entity\Bank;

use App\Entity\User;
use App\Repository\Bank\AccountShareRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AccountShareRepository::class)
 * @ORM\Table(name="bank_account_share")
 */
#[UniqueEntity(
    fields: ['account', 'user'],
    message: 'This account is already shared for this user.',
    errorPath: 'user',
)]
class AccountShare
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="accountShares")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="accountShares", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="AccountShareType")
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isType($type): bool
    {
        return $this->getType() === $type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }
}

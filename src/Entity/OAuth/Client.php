<?php

namespace App\Entity\OAuth;

use App\Repository\OAuth\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ORM\Table(name="oauth_client")
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $redirectUri;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActif;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isConfidential;

    public function __construct()
    {
        $this->identifier = Uuid::uuid4();
        $this->isActif = true;
        $this->isConfidential = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
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

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    public function isConfidential(): ?bool
    {
        return $this->isConfidential;
    }

    public function setIsConfidential(bool $isConfidential): self
    {
        $this->isConfidential = $isConfidential;

        return $this;
    }
}

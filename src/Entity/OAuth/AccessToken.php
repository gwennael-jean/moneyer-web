<?php

namespace App\Entity\OAuth;

use App\Entity\User;
use App\Repository\OAuth\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccessTokenRepository::class)
 * @ORM\Table(name="oauth_access_token")
 */
class AccessToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $identifier;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=RefreshToken::class, mappedBy="accessToken", cascade={"persist", "remove"})
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiryDateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRevoked;

    public function __construct()
    {
        $this->isRevoked = false;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    public function getRefreshToken(): ?RefreshToken
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(RefreshToken $refreshToken): self
    {
        // set the owning side of the relation if necessary
        if ($refreshToken->getAccessToken() !== $this) {
            $refreshToken->setAccessToken($this);
        }

        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpiryDateTime(): ?\DateTimeInterface
    {
        return $this->expiryDateTime;
    }

    public function setExpiryDateTime(\DateTimeInterface $expiryDateTime): self
    {
        $this->expiryDateTime = $expiryDateTime;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->getExpiryDateTime()->getTimestamp() < (new \DateTime())->getTimestamp();
    }

    public function isRevoked(): ?bool
    {
        return $this->isRevoked;
    }

    public function setIsRevoked(bool $isRevoked): self
    {
        $this->isRevoked = $isRevoked;

        return $this;
    }
}

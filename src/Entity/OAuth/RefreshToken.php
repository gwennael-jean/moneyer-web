<?php

namespace App\Entity\OAuth;

use App\Repository\OAuth\RefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RefreshTokenRepository::class)
 * @ORM\Table(name="oauth_refresh_token")
 */
class RefreshToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identifier;

    /**
     * @ORM\OneToOne(targetEntity=AccessToken::class, inversedBy="refreshToken", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $accessToken;

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

    public function getAccessToken(): ?AccessToken
    {
        return $this->accessToken;
    }

    public function setAccessToken(AccessToken $accessToken): self
    {
        $this->accessToken = $accessToken;

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

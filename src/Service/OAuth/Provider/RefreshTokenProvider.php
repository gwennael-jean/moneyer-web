<?php

namespace App\Service\OAuth\Provider;

use App\Entity\OAuth\RefreshToken;
use App\Repository\OAuth\RefreshTokenRepository;
use App\Service\OAuth\DataTransformer\RefreshTokenTransformer;
use App\Service\OAuth\Model\RefreshToken as ModelRefreshToken;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenProvider implements RefreshTokenRepositoryInterface
{
    /**
     * @var ArrayCollection
     */
    private $entities;

    public function __construct(
        private RefreshTokenRepository $refreshTokenRepository,
        private RefreshTokenTransformer $refreshTokenTransformer,
        private EntityManagerInterface $entityManager,
    )
    {
        $this->entities = new ArrayCollection();
    }

    public function getEntity($identifier): ?RefreshToken
    {
        if (!$this->entities->containsKey($identifier)) {
            $token = $this->refreshTokenRepository->findOneByIdentifier($identifier);

            if (null === $token) {
                return null;
            }

            $this->entities->set($identifier, $token);
        }

        return $this->entities->get($identifier);
    }

    /**
     * @return ModelRefreshToken
     */
    #[Pure]
    public function getNewRefreshToken(): ModelRefreshToken
    {
        return new ModelRefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $entity = $this->refreshTokenTransformer->reverseTransform($refreshTokenEntity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Cette méthode est appelée lorsqu'un jeton d'actualisation est utilisé pour réémettre un jeton d'accès.
     * Le jeton d'actualisation d'origine est révoqué, un nouveau jeton d'actualisation est émis.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        $token = $this->getEntity($tokenId);

        $token->setIsRevoked(true);

        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }

    /**
     * Cette méthode est appelée lorsqu'un jeton d'actualisation est utilisé pour émettre un nouveau jeton d'accès.
     * Renvoie true si le jeton d'actualisation a été révoqué manuellement avant son expiration.
     * Si le jeton est toujours valide, retournez false.
     *
     * @param string $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        $token = $this->getEntity($tokenId);

        return !(null !== $token && !$token->isRevoked() && !$token->isExpired());
    }

}
<?php

namespace App\Service\OAuth\Provider;

use App\Entity\OAuth\AccessToken as EntityAccessToken;
use App\Service\OAuth\Model\AccessToken as ModelAccessToken;
use App\Repository\OAuth\AccessTokenRepository;
use App\Service\OAuth\DataTransformer\AccessTokenTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenProvider implements AccessTokenRepositoryInterface
{
    /**
     * @var ArrayCollection
     */
    private $entities;

    #[Pure]
    public function __construct(
        private AccessTokenRepository $accessTokenRepository,
        private AccessTokenTransformer $accessTokenTransformer,
        private EntityManagerInterface $entityManager,
    )
    {
        $this->entities = new ArrayCollection();
    }

    public function getEntity(string $accessTokenIdentifier): ?EntityAccessToken
    {
        if (!$this->entities->containsKey($accessTokenIdentifier)) {
            $accessToken = $this->accessTokenRepository->findOneByIdentifier($accessTokenIdentifier);

            if (null === $accessToken) {
                return null;
            }

            $this->entities->set($accessTokenIdentifier, $accessToken);
        }

        return $this->entities->get($accessTokenIdentifier);
    }

    /**
     * Cette méthode doit renvoyer une implémentation de AccessTokenEntityInterface
     *
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param null $userIdentifier
     * @return ModelAccessToken
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = new ModelAccessToken();

        $token->setClient($clientEntity);
        $token->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }

        return $token;
    }

    /**
     * Lorsqu'un nouveau jeton d'accès est créé, cette méthode sera appelée.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $entity = $this->accessTokenTransformer->reverseTransform($accessTokenEntity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Cette méthode est appelée lorsqu'un jeton d'actualisation est utilisé pour réémettre un jeton d'accès.
     * Le jeton d'accès d'origine est révoqué, un nouveau jeton d'accès est émis.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        $entity = $this->getEntity($tokenId);

        $entity->setIsRevoked(true);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Cette méthode est appelée lorsqu'un jeton d'accès est validé par le middleware du serveur de ressources.
     * Renvoie true si le jeton d'accès a été révoqué manuellement avant son expiration.
     * Si le jeton est toujours valide, retournez false.
     *
     * @param string $tokenId
     * @return bool
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $token = $this->getEntity($tokenId);

        return !(null !== $token && !$token->isRevoked() && !$token->isExpired());
    }
}
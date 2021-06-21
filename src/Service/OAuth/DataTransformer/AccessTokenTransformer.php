<?php

namespace App\Service\OAuth\DataTransformer;

use App\Service\OAuth\Model\AccessToken as ModelAccessToken;
use App\Entity\OAuth\AccessToken as EntityAccessToken;
use App\Service\OAuth\Provider\ClientProvider;
use App\Service\OAuth\Provider\UserProvider;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AccessTokenTransformer implements DataTransformerInterface
{
    public function __construct(
        private ClientProvider $clientProvider,
        private UserProvider $userProvider
    )
    {
    }

    /**
     * @param EntityAccessToken $value
     * @return ModelAccessToken
     */
    public function transform($value): ModelAccessToken
    {
        if (!$value instanceof EntityAccessToken) {
            throw new TransformationFailedException(sprintf("argument 1 must be instance of %s", EntityAccessToken::class));
        }
        
         $token = new ModelAccessToken();

        return $token;
    }

    /**
     * @param ModelAccessToken $value
     * @return EntityAccessToken
     */
    public function reverseTransform($value): EntityAccessToken
    {
        if (!$value instanceof ModelAccessToken) {
            throw new TransformationFailedException(sprintf("argument 1 must be instance of %s", ModelAccessToken::class));
        }

        $token = new EntityAccessToken();

        $client = $this->clientProvider->getEntity($value->getClient()->getIdentifier());

        $user = $this->userProvider->getEntity($value->getUserIdentifier());

        $token
            ->setIdentifier($value->getIdentifier())
            ->setClient($client)
            ->setUser($user)
            ->setExpiryDateTime($value->getExpiryDateTime());

        return $token;
    }

}
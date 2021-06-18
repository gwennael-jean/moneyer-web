<?php

namespace App\Service\OAuth\DataTransformer;

use App\Service\OAuth\Model\RefreshToken as ModelRefreshToken;
use App\Entity\OAuth\RefreshToken as EntityRefreshToken;
use App\Service\OAuth\Provider\AccessTokenProvider;
use App\Service\OAuth\Provider\ClientProvider;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RefreshTokenTransformer implements DataTransformerInterface
{
    public function __construct(
        private AccessTokenProvider $accessTokenProvider
    )
    {
    }

    /**
     * @param EntityRefreshToken $value
     * @return ModelRefreshToken
     */
    public function transform($value): ModelRefreshToken
    {
        if (!$value instanceof EntityRefreshToken) {
            throw new TransformationFailedException(sprintf("argument 1 must be instance of %s", EntityRefreshToken::class));
        }
        
         $token = new ModelRefreshToken();

        return $token;
    }

    /**
     * @param ModelRefreshToken $value
     * @return EntityRefreshToken
     */
    public function reverseTransform($value): EntityRefreshToken
    {
        if (!$value instanceof ModelRefreshToken) {
            throw new TransformationFailedException(sprintf("argument 1 must be instance of %s", ModelRefreshToken::class));
        }

        $token = new EntityRefreshToken();

        $accessToken = $this->accessTokenProvider->getEntity($value->getAccessToken()->getIdentifier());

        $token
            ->setIdentifier($value->getIdentifier())
            ->setAccessToken($accessToken)
            ->setExpiryDateTime($value->getExpiryDateTime());

        return $token;
    }

}
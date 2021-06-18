<?php

namespace App\Service\OAuth\DataTransformer;

use App\Service\OAuth\Model\Client as ModelClient;
use App\Entity\OAuth\Client as EntityClient;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ClientTransformer implements DataTransformerInterface
{
    /**
     * @param EntityClient $value
     * @return ModelClient
     */
    public function transform($value): ModelClient
    {
        if (!$value instanceof EntityClient) {
            throw new TransformationFailedException(sprintf("argument 1 must be instance of %s", EntityClient::class));
        }

        return new ModelClient($value);
    }

    /**
     * @param ModelClient $value
     * @return EntityClient
     */
    public function reverseTransform($value): EntityClient
    {
        if (!$value instanceof ModelClient) {
            throw new TransformationFailedException(sprintf("argument 1 must be instance of %s", ModelClient::class));
        }

        $entity = new EntityClient();

        $entity
            ->setIdentifier($value->getIdentifier())
            ->setName($value->getName())
            ->setRedirectUri($value->getRedirectUri())
            ->setIsConfidential($value->isConfidential());

        return $entity;
    }

}
<?php

namespace App\Service\OAuth\Model;

use App\Entity\OAuth\Client as EntityClient;
use JetBrains\PhpStorm\Pure;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class Client implements ClientEntityInterface
{
    use EntityTrait;
    use ClientTrait;

    #[Pure]
    public function __construct(EntityClient $client)
    {
        $this->identifier = $client->getIdentifier();
        $this->name = $client->getName();
        $this->redirectUri = $client->getRedirectUri();
        $this->isConfidential = $client->isConfidential();
    }
}
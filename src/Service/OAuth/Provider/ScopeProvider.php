<?php

namespace App\Service\OAuth\Provider;

use App\Service\OAuth\Model\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeProvider implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier)
    {
        $scope = new Scope();
        $scope->setIdentifier($identifier);
        return $scope;
    }

    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        return array_map(fn (string $scope) => $this->getScopeEntityByIdentifier($scope), $scopes);
    }
}
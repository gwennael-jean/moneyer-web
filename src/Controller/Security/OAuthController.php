<?php

namespace App\Controller\Security;

use App\Service\OAuth\OAuthServerResponseFactory;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    public function __construct(
        private OAuthServerResponseFactory $authServerResponseFactory
    )
    {
    }

    #[Route('/oauth/token', name: 'oauth_token')]
    public function token(Request $request, PasswordGrant $grant): Response
    {
        return $this->authServerResponseFactory->generateResponseByGrant($request, $grant);
    }

    #[Route('/oauth/refresh', name: 'oauth_refresh')]
    public function refresh(Request $request, RefreshTokenGrant $grant): Response
    {
        return $this->authServerResponseFactory->generateResponseByGrant($request, $grant);
    }
}

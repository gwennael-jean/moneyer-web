<?php

namespace App\Security\Authenticator;

use App\Security\Authenticator\Exception\AuthenticationWithResponseException;
use Laminas\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AccessTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ResourceServer $resourceServer
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('authorization') && !!$request->headers->get('authorization');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        try {
            $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
        } catch (OAuthServerException $exception) {
            $response = (new HttpFoundationFactory())
                ->createResponse($exception->generateHttpResponse(new Psr7Response()));
            throw new AuthenticationWithResponseException($response);
        }

        return new SelfValidatingPassport(new UserBadge($psrRequest->getAttribute('oauth_user_id')));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($exception instanceof AuthenticationWithResponseException) {
            return $exception->getResponse();
        }

        return new JsonResponse("Username could not be found");
    }
}

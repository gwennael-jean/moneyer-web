<?php

namespace App\Service\OAuth;

use Laminas\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuthServerResponseFactory
{
    public function __construct(
        private AuthorizationServer $authorizationServer
    )
    {
    }

    public function generateResponseByGrant(Request $request, GrantTypeInterface $grantType)
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        $grantType->setRefreshTokenTTL(new \DateInterval('P3M'));

        try {
            $this->authorizationServer->enableGrantType($grantType, new \DateInterval('PT1H'));

            $response = $this->authorizationServer->respondToAccessTokenRequest($psrRequest, new Psr7Response());
        } catch (OAuthServerException $exception) {
            $response = $exception->generateHttpResponse(new Psr7Response());
        } catch (\Exception $e) {
            $response = new Psr7Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            $response = new Psr7Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return (new HttpFoundationFactory())
            ->createResponse($response);
    }
}
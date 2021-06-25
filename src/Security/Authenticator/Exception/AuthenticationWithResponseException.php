<?php

namespace App\Security\Authenticator\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

class AuthenticationWithResponseException extends AuthenticationException
{
    public function __construct(private Response $response, $code = 0, Throwable $previous = null)
    {
        parent::__construct($response->getContent(), $code, $previous);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
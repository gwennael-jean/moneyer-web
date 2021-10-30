<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestHandler
{
    private ?\DateTime $date = null;

    public function __construct(
        private RequestStack $requestStack
    )
    {
    }

    public function getDate(): \DateTime
    {
        if (null === $this->date) {
            $date = $this->requestStack->getCurrentRequest()->get('date');
            $this->date = null !== $date
                ? \DateTime::createFromFormat('Y-m', $date)
                : new \DateTime();
        }

        return $this->date;
    }
}

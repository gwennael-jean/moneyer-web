<?php

namespace App\Twig\Bank;

use App\Service\Provider\Bank\ResourceProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AccountExtension extends AbstractExtension
{
    public function __construct(
        private ResourceProvider $resourceProvider
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('resources', [$this->resourceProvider, 'getByAccounts']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('resources', [$this->resourceProvider, 'getByAccounts']),
        ];
    }

    public function doSomething($value)
    {
        // ...
    }
}

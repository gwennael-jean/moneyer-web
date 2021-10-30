<?php

namespace App\Twig\Bank;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\ChargeDistribution;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ChargeDistributionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('icon_class', [$this, 'getIconClass']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon_class', [$this, 'getIconClass']),
        ];
    }

    public function getIconClass(ChargeDistribution $chargeDistribution)
    {
        switch ($chargeDistribution->getType()) {
            case ChargeDistributionType::VIEW: return "fa-eye";
            case ChargeDistributionType::FIFTY_FIFTY: return "fa-divide";
            case ChargeDistributionType::RESOURCE_PERCENT: return "fa-percent";
        }

        return "fa-ban";
    }
}

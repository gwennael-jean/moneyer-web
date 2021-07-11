<?php

namespace App\DBAL\Types\Bank;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class ChargeDistributionType extends AbstractEnumType
{
    public const VIEW = 'VIEW';
    public const FIFTY_FIFTY = 'FIFTY_FIFTY';
    public const RESOURCE_PERCENT = 'RESOURCE_PERCENT';

    protected static $choices = [
        self::VIEW => 'View',
        self::FIFTY_FIFTY => 'Fifty fifty',
        self::RESOURCE_PERCENT => 'Resource Percent',
    ];
}
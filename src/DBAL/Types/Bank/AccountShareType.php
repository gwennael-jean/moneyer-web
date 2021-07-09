<?php

namespace App\DBAL\Types\Bank;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class AccountShareType extends AbstractEnumType
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';

    protected static $choices = [
        self::VIEW => 'View',
        self::EDIT => 'Edit'
    ];
}
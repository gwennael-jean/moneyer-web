<?php

namespace App\DBAL\Types\Bank;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class AccountShareType extends AbstractEnumType
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    protected static $choices = [
        self::VIEW => 'View',
        self::EDIT => 'Edit'
    ];
}
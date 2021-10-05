<?php

namespace App\DBAL\Types;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MonthType extends Type
{
    const NAME = 'MonthType';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "DATE";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new DateTime($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null !== $value) {
            if (!$value instanceof DateTimeInterface) {
                throw new InvalidArgumentException(sprintf("$value must be instance of %s", DateTimeInterface::class));
            }

            return $value->format('Y-m') . '-01';
        }

        return null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return self::NAME;
    }

}

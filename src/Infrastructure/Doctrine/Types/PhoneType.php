<?php

namespace App\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Types\StringType;
use App\Domain\ValueObject\User\Phone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Webmozart\Assert\Assert as WebmozartAssert;

class PhoneType extends StringType
{
    public const NAME = 'phone';

    /**
     * @param mixed|Phone $value
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        WebmozartAssert::isInstanceOf($value, Phone::class);

        return $value->toString();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Phone
    {
        if ($value === null) {
            return null;
        }

        WebmozartAssert::stringNotEmpty($value, 'Phone type is represented by a varchar database type. Not-empty string or null values allowed');

        return new Phone($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}

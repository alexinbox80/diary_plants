<?php

namespace App\Infrastructure\Doctrine\Types;

use App\Domain\ValueObject\Email;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Webmozart\Assert\Assert as WebmozartAssert;

class EmailType extends StringType
{
    public const NAME = 'email';

    /**
     * @param mixed|Email $value
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        WebmozartAssert::isInstanceOf($value, Email::class);

        return $value->toString();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        if ($value === null) {
            return null;
        }

        WebmozartAssert::stringNotEmpty($value, 'Email type is represented by a varchar database type. Not-empty string or null values allowed');

        return new Email($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}

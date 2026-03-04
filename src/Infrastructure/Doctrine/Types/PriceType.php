<?php

namespace App\Infrastructure\Doctrine\Types;

use InvalidArgumentException;
use App\Domain\ValueObject\Price;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Webmozart\Assert\Assert as WebmozartAssert;

final class PriceType extends StringType
{
    public const NAME = 'price';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        WebmozartAssert::isInstanceOf($value, Price::class);

        /** @var Price $value */
        return $value->toString();
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Price
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new ConversionException(
                'Could not convert database value "' . $value . '" to Doctrine Type ' . $this->getName()
            );
        }

        try {
            return Price::fromString($value);
        } catch (InvalidArgumentException $e) {
            throw new ConversionException(
                'Could not convert database value "' . $value . '" to Doctrine Type ' . $this->getName()
            );
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}

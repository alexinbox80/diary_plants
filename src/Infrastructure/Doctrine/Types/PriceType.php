<?php

namespace App\Infrastructure\Doctrine\Types;

use App\Domain\ValueObject\Price;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class PriceType extends StringType
{
    public const NAME = 'Price';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        Assert::isInstanceOf($value, Price::class);

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

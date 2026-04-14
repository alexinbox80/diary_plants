<?php

namespace Unit\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use App\Infrastructure\Doctrine\Types\PriceType;

class PriceTypeTest extends TestCase
{
    private PriceType $type;
    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        if (!Type::hasType(PriceType::NAME)) {
            Type::addType(PriceType::NAME, PriceType::class);
        }

        $this->type = Type::getType(PriceType::NAME);
        $this->platform = new PostgreSQLPlatform();
    }

    public function testConvertToDatabaseValueReturnsString(): void
    {
        $priceStr = '1500 RUR';
        $priceVo = Price::fromString($priceStr);

        $result = $this->type->convertToDatabaseValue($priceVo, $this->platform);

        $this->assertEquals($priceStr, $result);
    }

    public function testConvertToDatabaseValueReturnsNullOnEmpty(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->type->convertToDatabaseValue('', $this->platform));
    }

    public function testConvertToPHPValueReturnsPriceObject(): void
    {
        $priceStr = '500 USD';

        $result = $this->type->convertToPHPValue($priceStr, $this->platform);

        $this->assertInstanceOf(Price::class, $result);
        $this->assertEquals($priceStr, $result->toString());
    }

    public function testConvertToPHPValueThrowsExceptionOnInvalidFormat(): void
    {
        // Если Price::fromString выбросит InvalidArgumentException,
        // тип должен обернуть его в ConversionException
        $this->expectException(ConversionException::class);
        $this->type->convertToPHPValue('invalid_price', $this->platform);
    }

    public function testConvertToPHPValueThrowsExceptionOnNonString(): void
    {
        $this->expectException(ConversionException::class);
        $this->type->convertToPHPValue(12345, $this->platform);
    }

    public function testRequiresSQLCommentHintReturnsTrue(): void
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }
}

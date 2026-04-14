<?php

namespace Unit\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User\Phone;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use App\Infrastructure\Doctrine\Types\PhoneType;

class PhoneTypeTest extends TestCase
{
    private PhoneType $type;
    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        if (!Type::hasType(PhoneType::NAME)) {
            Type::addType(PhoneType::NAME, PhoneType::class);
        }

        $this->type = Type::getType(PhoneType::NAME);
        $this->platform = new PostgreSQLPlatform();
    }

    public function testConvertToDatabaseValueReturnsString(): void
    {
        $phoneStr = '+79991234567';
        $phoneVo = new Phone($phoneStr);

        $result = $this->type->convertToDatabaseValue($phoneVo, $this->platform);

        $this->assertEquals($phoneStr, $result);
    }

    public function testConvertToDatabaseValueReturnsNullOnNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testConvertToPHPValueReturnsPhoneObject(): void
    {
        $phoneStr = '+79991234567';

        $result = $this->type->convertToPHPValue($phoneStr, $this->platform);

        $this->assertInstanceOf(Phone::class, $result);
        $this->assertEquals($phoneStr, $result->toString());
    }

    public function testConvertToPHPValueReturnsNullOnNull(): void
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueThrowsExceptionOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone type is represented by a varchar database type');

        $this->type->convertToPHPValue('', $this->platform);
    }
}

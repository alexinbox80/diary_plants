<?php

namespace Unit\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use App\Domain\ValueObject\OId;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Types\ConversionException;
use App\Infrastructure\Doctrine\Types\OIdType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

class OIdTypeTest extends TestCase
{
    private OIdType $type;
    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        // Регистрируем тип, если он еще не зарегистрирован
        if (!Type::hasType(OIdType::NAME)) {
            Type::addType(OIdType::NAME, OIdType::class);
        }

        $this->type = Type::getType(OIdType::NAME);
        $this->platform = new PostgreSQLPlatform();
    }

    public function testConvertToDatabaseValueReturnsString(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $oid = OId::fromString($uuid);

        $result = $this->type->convertToDatabaseValue($oid, $this->platform);

        $this->assertEquals($uuid, $result);
    }

    public function testConvertToDatabaseValueReturnsNullOnEmpty(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->type->convertToDatabaseValue('', $this->platform));
    }

    public function testConvertToPHPValueReturnsObject(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $result = $this->type->convertToPHPValue($uuid, $this->platform);

        $this->assertInstanceOf(OId::class, $result);
        $this->assertEquals($uuid, $result->toString());
    }

    public function testConvertToPHPValueThrowsExceptionOnInvalidUuid(): void
    {
        $this->expectException(ConversionException::class);
        $this->type->convertToPHPValue('invalid-uuid', $this->platform);
    }

    public function testGetSQLDeclarationForPostgres(): void
    {
        // Проверяем, что для Postgres используется тип UUID
        $sql = $this->type->getSQLDeclaration([], $this->platform);
        $this->assertEquals('UUID', $sql);
    }
}

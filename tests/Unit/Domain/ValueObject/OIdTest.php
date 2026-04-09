<?php

namespace Unit\Domain\ValueObject;

use App\Domain\ValueObject\OId;
use PHPUnit\Framework\TestCase;

class OIdTest extends TestCase
{
    public function testNextGeneratesValidUuid(): void
    {
        $oid = OId::next();

        $this->assertInstanceOf(OId::class, $oid);
        // Проверяем формат UUIDv4 (8-4-4-4-12 символов)
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $oid->toString()
        );
    }

    public function testFromString(): void
    {
        $uuidStr = '550e8400-e29b-41d4-a716-446655440000';
        $oid = OId::fromString($uuidStr);

        $this->assertEquals($uuidStr, $oid->toString());
        $this->assertEquals($uuidStr, (string)$oid);
    }

    public function testIsEqual(): void
    {
        $uuidStr = '550e8400-e29b-41d4-a716-446655440000';
        $oid1 = OId::fromString($uuidStr);
        $oid2 = OId::fromString($uuidStr);
        $oid3 = OId::next();

        $this->assertTrue($oid1->isEqual($oid2));
        $this->assertFalse($oid1->isEqual($oid3));
    }

    public function testToBinaryAndFromBinary(): void
    {
        $original = OId::next();
        $binary = $original->toBinary();

        $fromBinary = OId::fromBinary($binary);

        $this->assertTrue($original->isEqual($fromBinary));
        $this->assertEquals($original->toString(), $fromBinary->toString());
    }

    public function testToRfc4122(): void
    {
        $uuidStr = '550e8400-e29b-41d4-a716-446655440000';
        $oid = OId::fromString($uuidStr);

        $this->assertEquals($uuidStr, $oid->toRfc4122());
    }
}

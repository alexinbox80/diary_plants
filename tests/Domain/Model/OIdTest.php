<?php

namespace App\Tests\Domain\Model;

use App\Domain\Model\OId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class OIdTest extends TestCase
{
    public function testNextGeneratesValidUuid(): void
    {
        $oid = OId::next();
        $this->assertInstanceOf(OId::class, $oid);
        $this->assertIsString((string) $oid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', (string) $oid);
    }

    public function testFromStringCreatesOIdFromRfc4122String(): void
    {
        $uuid = UuidV4::v4();
        $oid = OId::fromString($uuid->toRfc4122());
        $this->assertInstanceOf(OId::class, $oid);
        $this->assertSame($uuid->toRfc4122(), (string) $oid);
    }

    public function testFromBinaryCreatesOIdFromBinaryRepresentation(): void
    {
        $uuid = UuidV4::v4();
        $binary = $uuid->toBinary();
        $oid = OId::fromBinary($binary);
        $this->assertInstanceOf(OId::class, $oid);
        $this->assertSame($uuid->toBinary(), $oid->toBinary());
    }

    public function testIsEqualComparesTwoOIds(): void
    {
        $oid1 = OId::next();
        $oid2 = OId::fromString((string) $oid1);
        $oid3 = OId::next();

        $this->assertTrue($oid1->isEqual($oid2));
        $this->assertFalse($oid1->isEqual($oid3));
    }

    public function testCanBeConvertedToStringAndBack(): void
    {
        $oid = OId::next();
        $string = (string) $oid;
        $oid2 = OId::fromString($string);

        $this->assertSame($oid->toString(), $oid2->toString());
        $this->assertTrue($oid->isEqual($oid2));
    }

    public function testCanBeConvertedToBinaryAndBack(): void
    {
        $oid = OId::next();
        $binary = $oid->toBinary();
        $oid2 = OId::fromBinary($binary);

        $this->assertSame($oid->toBinary(), $oid2->toBinary());
        $this->assertTrue($oid->isEqual($oid2));
    }

    public function testToStringReturnsRfc4122Format(): void
    {
        $oid = OId::next();
        $this->assertSame($oid->toRfc4122(), (string) $oid);
        $this->assertSame($oid->toRfc4122(), $oid->toString());
    }
}

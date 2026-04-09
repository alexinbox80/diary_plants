<?php

namespace Unit\Domain\ValueObject\Plant;

use App\Domain\ValueObject\OId;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Plant\PlantIdentifier;

class PlantIdentifierTest extends TestCase
{
    private function createRealOid(): OId
    {
        return OId::next();
    }

    public function testInitialization(): void
    {
        $oid = $this->createRealOid();
        $qrLink = 'https://storage.yandex';

        $identifier = new PlantIdentifier($oid, $qrLink);

        $this->assertSame($oid, $identifier->getOid());
        $this->assertEquals($qrLink, $identifier->getQrCodeLink());
    }

    public function testWithQrCodeLinkCreatesNewInstance(): void
    {
        $oid = $this->createRealOid();
        $original = new PlantIdentifier($oid, null);
        $newLink = 'https://new-link.com';

        $updated = $original->withQrCodeLink($newLink);

        // Проверяем, что создался новый объект, а старый не изменился
        $this->assertNotSame($original, $updated);
        $this->assertNull($original->getQrCodeLink());
        $this->assertEquals($newLink, $updated->getQrCodeLink());

        // Проверяем, что OId перенесся в новый объект
        $this->assertSame($original->getOid(), $updated->getOid());
    }

    public function testGetQrCodeLinkCanBeNull(): void
    {
        $identifier = new PlantIdentifier($this->createRealOid());
        $this->assertNull($identifier->getQrCodeLink());
    }
}


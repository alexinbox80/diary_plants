<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Attachment;
use App\Domain\Entity\Plant;
use App\Domain\Model\OId;
use App\Domain\Model\Price;
use App\Domain\Model\Currency;
use DateTime;
use PHPUnit\Framework\TestCase;

class PlantTest extends TestCase
{
    public function testConstructorInitializesAllProperties(): void
    {
        $oid = OId::next();
        $price = new Price(100, Currency::USD);

        $purchaseDate = new DateTime();
        $vaccinationDate = new DateTime();
        $plantingDate = new DateTime();

        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: new DateTime(),
            description: 'A beautiful photo',
            attachableId: 1,
            attachableType: Plant::class,
        );

        $plant = new Plant(
            title: 'Rose',
            room: 'Living Room',
            isShown: true,
            attachment: null,
            description: 'A beautiful flower',
            purchaseDate: $purchaseDate,
            vaccinationDate: $vaccinationDate,
            plantingDate: $plantingDate,
            manufacturer: 'GreenHouse Inc.',
            price: $price,
            soil: 'Clay'
        );

        $this->assertEquals('Rose', $plant->getTitle());
        $this->assertEquals('Living Room', $plant->getRoom());
        $this->assertTrue($plant->isShown());
        $this->assertEquals('A beautiful flower', $plant->getDescription());
        $this->assertEquals($purchaseDate, $plant->getPurchaseDate());
        $this->assertEquals($vaccinationDate, $plant->getVaccinationDate());
        $this->assertEquals($plantingDate, $plant->getPlantingDate());
        $this->assertEquals('GreenHouse Inc.', $plant->getManufacturer());
        $this->assertEquals($price, $plant->getPrice());
        $this->assertEquals('Clay', $plant->getSoil());

        $this->assertInstanceOf(OId::class, $plant->getOid());
        $this->assertStringStartsWith('data:image/png;base64,', $plant->getQrCodeBase64());

        $this->assertSame(Plant::class, $attachment->getAttachableType());
        $this->assertNotNull($attachment->getAttachableId());
        //$this->assertSame($attachment, $plant->getAttachment());
    }

    public function testChangeFieldsUpdatesAllProperties(): void
    {
        $oid = OId::next();
        $price = new Price(150, Currency::EUR);
        $purchaseDate = new DateTime();
        $vaccinationDate = new DateTime();
        $plantingDate = new DateTime();

        $plant = new Plant(
            title: 'Cactus',
            room: 'Office',
            isShown: false,
            attachment: null,
        );

        $plant->changeFields(
            title: 'Tulip',
            room: 'Garden',
            isShown: false,
            attachment: null,
            description: 'Spring flower',
            purchaseDate: $purchaseDate,
            vaccinationDate: $vaccinationDate,
            plantingDate: $plantingDate,
            manufacturer: 'FlowerCo',
            price: $price,
            soil: 'Sandy'
        );

        $this->assertEquals('Tulip', $plant->getTitle());
        $this->assertEquals('Garden', $plant->getRoom());
        $this->assertFalse($plant->isShown());
        //$this->assertEquals(789, $plant->getAttachmentId());
        $this->assertEquals('Spring flower', $plant->getDescription());
        $this->assertEquals($purchaseDate, $plant->getPurchaseDate());
        $this->assertEquals($vaccinationDate, $plant->getVaccinationDate());
        $this->assertEquals($plantingDate, $plant->getPlantingDate());
        $this->assertEquals('FlowerCo', $plant->getManufacturer());
        $this->assertEquals($price, $plant->getPrice());
        $this->assertEquals('Sandy', $plant->getSoil());

        $this->assertInstanceOf(OId::class, $plant->getOid());
        $this->assertStringStartsWith('data:image/png;base64,', $plant->getQrCodeBase64());
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $plant = new Plant(title: 'Test', room: 'Room', isShown: true, attachment: null);
        $plant->getId(); // id is null
    }
}

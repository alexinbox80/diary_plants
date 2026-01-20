<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Offspring;
use App\Domain\Entity\Attachment;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class OffspringTest extends TestCase
{
    private function getProperty($object, $property, $data): mixed
    {
        $reflection = new \ReflectionClass($object);
        $propertyRef = $reflection->getProperty($property);
        $propertyRef->setAccessible(true);
        if ($data) {
            $propertyRef->setValue($object, $data);
        }
        return $propertyRef->getValue($object);
    }

    public function testConstructorInitializesAllProperties(): void
    {
        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: new DateTime(),
            description: 'A beautiful photo',
        );

        $fruitingDate = new DateTimeImmutable();
        $floweringDate = new DateTimeImmutable();

        $offspring = new Offspring(
            //attachment: null,
            fruitingDate: $fruitingDate,
            floweringDate: $floweringDate,
            mass: 100,
            color: 'red',
            flavor: 'sweet',
            quantity: 5
        );

        $this->getProperty($offspring, 'id', 2);
        $this->getProperty($offspring, 'createdAt', new DateTimeImmutable());
        $this->getProperty($offspring, 'updatedAt', new DateTimeImmutable());

        //$this->assertSame(Offspring::class, $attachment->getAttachableType());
        //$this->assertNull($attachment->getAttachableId(), 'ID should be set after save');
        //this->assertSame($attachment, $offspring->getAttachment());
        $this->assertSame($fruitingDate, $offspring->getFruitingDate());
        $this->assertSame($floweringDate, $offspring->getFloweringDate());
        $this->assertSame(100, $offspring->getMass());
        $this->assertSame('red', $offspring->getColor());
        $this->assertSame('sweet', $offspring->getFlavor());
        $this->assertSame(5, $offspring->getQuantity());
    }

    public function testChangeFieldsUpdatesAllProperties(): void
    {
        $offspring = new Offspring();

        $this->getProperty($offspring, 'id', 1);
        $this->getProperty($offspring, 'createdAt', new DateTimeImmutable());
        $this->getProperty($offspring, 'updatedAt', new DateTimeImmutable());

        $newAttachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'New Image',
            photoDate: new DateTime(),
            description: 'Another photo'
        );

        $this->getProperty($newAttachment, 'id', 1);

        $newFruitingDate = new DateTimeImmutable();
        $newFloweringDate = new DateTimeImmutable();

        $offspring->changeFields(
            //attachment: $newAttachment,
            fruitingDate: $newFruitingDate,
            floweringDate: $newFloweringDate,
            mass: 200,
            color: 'green',
            flavor: 'sour',
            quantity: 10
        );

        //$this->assertSame(Attachment::class, $newAttachment->getAttachableType());
        $this->assertIsInt($offspring->getId());
        //$this->assertSame($newAttachment, $offspring->getAttachment());
        $this->assertSame($newFruitingDate, $offspring->getFruitingDate());
        $this->assertSame($newFloweringDate, $offspring->getFloweringDate());
        $this->assertSame(200, $offspring->getMass());
        $this->assertSame('green', $offspring->getColor());
        $this->assertSame('sour', $offspring->getFlavor());
        $this->assertSame(10, $offspring->getQuantity());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $offspring = new Offspring(
            fruitingDate: new DateTimeImmutable(),
            floweringDate: new DateTimeImmutable(),
            mass: 150,
            color: 'yellow',
            flavor: 'tangy',
            quantity: 3
        );

        $this->assertInstanceOf(DateTimeImmutable::class, $offspring->getFruitingDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $offspring->getFloweringDate());
        $this->assertSame(150, $offspring->getMass());
        $this->assertSame('yellow', $offspring->getColor());
        $this->assertSame('tangy', $offspring->getFlavor());
        $this->assertSame(3, $offspring->getQuantity());
    }

    public function testToArrayReturnsExpectedArray(): void
    {
        $newAttachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'New Image',
            photoDate: new DateTime(),
            description: 'Another photo'
        );

        $this->getProperty($newAttachment, 'id', 1);
        $this->getProperty($newAttachment, 'createdAt', new DateTimeImmutable());
        $this->getProperty($newAttachment, 'updatedAt', new DateTimeImmutable());

        $offspring = new Offspring(
            fruitingDate: new DateTime('2024-01-01'),
            floweringDate: new DateTime('2024-02-01'),
            mass: 150,
            color: 'yellow',
            flavor: 'tangy',
            quantity: 3
        );

        $this->getProperty($offspring, 'id', 1);
        $this->getProperty($offspring, 'createdAt', new DateTimeImmutable());
        $this->getProperty($offspring, 'updatedAt', new DateTimeImmutable());

        $array = $offspring->toArray($newAttachment);

        $this->assertArrayHasKey('id', $array);
        //$this->assertArrayHasKey('attachment', $array);
        $this->assertArrayHasKey('fruiting_date', $array);
        $this->assertArrayHasKey('flowering_date', $array);
        $this->assertArrayHasKey('mass', $array);
        $this->assertArrayHasKey('color', $array);
        $this->assertArrayHasKey('flavor', $array);
        $this->assertArrayHasKey('quantity', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $offspring = new Offspring();
        $offspring->getId();
    }
}

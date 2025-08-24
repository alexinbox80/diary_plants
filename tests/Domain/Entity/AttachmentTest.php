<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Attachment;
use DateTime;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
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
        $photoDate = new DateTime();
        $description = 'A beautiful photo';
        $attachableId = 123;
        $attachableType = \App\Domain\Entity\Plant::class;

        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: $photoDate,
            description: $description,
            attachableId: $attachableId,
            attachableType: $attachableType
        );

        $this->assertSame('https://example.com/image.jpg', $attachment->getPhotoLink());
        $this->assertSame('Image', $attachment->getTitle());
        $this->assertSame($description, $attachment->getDescription());
        $this->assertSame($photoDate, $attachment->getPhotoDate());
       // $this->assertSame($attachableId, $attachment->getAttachable()->getId());
        $this->assertSame($attachableType, $attachment->getAttachableType());
    }

    public function testChangeFieldsUpdatesAllProperties(): void
    {
        $photoDate = new DateTime();
        $newPhotoDate = (clone $photoDate)->modify('+1 day');
        $description = 'Another photo';
        $attachableId = 456;
        $attachableType = \App\Domain\Entity\Plant::class;

        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: $photoDate
        );

        $attachment->changeFields(
            photoLink: 'https://example.com/new-image.jpg',
            title: 'New Image',
            description: $description,
            photoDate: $newPhotoDate,
            attachableId: $attachableId,
            attachableType: $attachableType
        );

        $this->assertSame('https://example.com/new-image.jpg', $attachment->getPhotoLink());
        $this->assertSame('New Image', $attachment->getTitle());
        $this->assertSame($description, $attachment->getDescription());
        $this->assertSame($newPhotoDate, $attachment->getPhotoDate());
        //$this->assertSame($attachableId, $attachment->getAttachableId());
        $this->assertSame($attachableType, $attachment->getAttachableType());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: new DateTime()
        );

        $this->assertInstanceOf(DateTime::class, $attachment->getPhotoDate());
        $this->assertSame('https://example.com/image.jpg', $attachment->getPhotoLink());
        $this->assertSame('Image', $attachment->getTitle());
        $this->assertNull($attachment->getDescription());
        //$this->assertNull($attachment->getAttachableId());
        $this->assertNull($attachment->getAttachableType());
    }

    public function testSettersUpdateValues(): void
    {
        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: new DateTime()
        );

       // $attachment->setAttachableId(789);
        $attachment->setAttachableType(\App\Domain\Entity\Plant::class);

        //$this->assertSame(789, $attachment->getAttachableId());
        $this->assertSame(\App\Domain\Entity\Plant::class, $attachment->getAttachableType());
    }

    public function testToArrayReturnsExpectedArray(): void
    {
        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: new DateTime('2024-01-01'),
            description: 'A beautiful photo',
            attachableId: 123,
            attachableType: \App\Domain\Entity\Plant::class
        );

        // Применяем трейты CreatedAtTrait и UpdatedAtTrait
        //$attachment->touch(); // чтобы установить updatedAt

        $this->getProperty($attachment, 'id', 1);
        $this->getProperty($attachment, 'createdAt', new DateTime());
        $this->getProperty($attachment, 'updatedAt', new DateTime());

        $array = $attachment->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('photo_link', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('photo_date', $array);
        //$this->assertArrayHasKey('attachable_id', $array);
        //$this->assertArrayHasKey('attachable_type', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);

        $this->assertSame('https://example.com/image.jpg', $array['photo_link']);
        $this->assertSame('Image', $array['title']);
        $this->assertSame('A beautiful photo', $array['description']);
        $this->assertSame('2024-01-01', $array['photo_date']);
        //$this->assertSame(123, $array['attachable_id']);
        //$this->assertSame(\App\Domain\Entity\Plant::class, $array['attachable_type']);
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $attachment = new Attachment(
            photoLink: 'https://example.com/image.jpg',
            title: 'Image',
            photoDate: new DateTime()
        );

        $attachment->getId();
    }
}

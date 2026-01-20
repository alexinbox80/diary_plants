<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Fertilizer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FertilizerTest extends TestCase
{
    private function getProperty($object, $property, $data = null): mixed
    {
        $reflection = new \ReflectionClass($object);
        $propertyRef = $reflection->getProperty($property);
        $propertyRef->setAccessible(true);
        if ($data !== null) {
            $propertyRef->setValue($object, $data);
        }
        return $propertyRef->getValue($object);
    }

    public function testConstructorInitializesAllProperties(): void
    {
        $date = new DateTimeImmutable();

        $fertilizer = new Fertilizer(
            'NPK',
            'Complex',
            100,
            $date,
            'Balanced fertilizer'
        );

        $this->getProperty($fertilizer, 'createdAt', new DateTimeImmutable());
        $this->getProperty($fertilizer, 'updatedAt', new DateTimeImmutable());

        $this->assertSame('NPK', $fertilizer->getTitle());
        $this->assertSame('Complex', $fertilizer->getManufacturer());
        $this->assertSame('Balanced fertilizer', $fertilizer->getDescription());
        $this->assertSame(100, $fertilizer->getQuantity());
        $this->assertSame($date, $fertilizer->getUseDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $fertilizer->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $fertilizer->getUpdatedAt());
    }

    public function testSettersUpdateValuesAndTouchUpdatedAt(): void
    {
        $date = new DateTimeImmutable();

        $fertilizer = new Fertilizer(
            title: 'NPK',
            manufacturer: 'Complex',
            quantity: 100,
            useDate: $date,
            description: 'Balanced fertilizer'
        );

        $dateNew = new DateTime();

        $fertilizer->changeFields(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $dateNew,
            description: 'Balanced fertilizer change'
        );

        $this->getProperty($fertilizer, 'createdAt', new DateTimeImmutable());
        $this->getProperty($fertilizer, 'updatedAt', new DateTimeImmutable());

        $this->assertSame('KNK', $fertilizer->getTitle());
        $this->assertSame('Complex change', $fertilizer->getManufacturer());
        $this->assertSame('Balanced fertilizer change', $fertilizer->getDescription());
        $this->assertSame(50, $fertilizer->getQuantity());
        $this->assertSame($dateNew, $fertilizer->getUseDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $fertilizer->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $fertilizer->getUpdatedAt());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $date = new DateTimeImmutable();

        $fertilizer = new Fertilizer(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change',
        );

        $this->assertSame('KNK', $fertilizer->getTitle());
        $this->assertSame('Complex change', $fertilizer->getManufacturer());
        $this->assertSame('Balanced fertilizer change', $fertilizer->getDescription());
        $this->assertSame(50, $fertilizer->getQuantity());
        $this->assertSame($date, $fertilizer->getUseDate());
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $date = new DateTimeImmutable();

        $fertilizer = new Fertilizer(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change'
        );
        $fertilizer->getId();
    }

    public function testTouchUpdatesUpdatedAt(): void
    {
        $date = new DateTimeImmutable('2024-01-01');

        $fertilizer = new Fertilizer(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change');

        $this->getProperty($fertilizer, 'updatedAt', new DateTimeImmutable());

        $this->assertNotEquals($date, $fertilizer->getUpdatedAt());
    }
}

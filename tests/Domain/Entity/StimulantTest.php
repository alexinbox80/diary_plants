<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Stimulant;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class StimulantTest extends TestCase
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

        $stimulant = new Stimulant(
            'NPK',
            'Complex',
            100,
            $date,
            'Balanced fertilizer'
        );

        $this->getProperty($stimulant, 'createdAt', new DateTimeImmutable());
        $this->getProperty($stimulant, 'updatedAt', new DateTimeImmutable());

        $this->assertSame('NPK', $stimulant->getTitle());
        $this->assertSame('Complex', $stimulant->getManufacturer());
        $this->assertSame('Balanced fertilizer', $stimulant->getDescription());
        $this->assertSame(100, $stimulant->getQuantity());
        $this->assertSame($date, $stimulant->getUseDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $stimulant->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $stimulant->getUpdatedAt());
    }

    public function testSettersUpdateValuesAndTouchUpdatedAt(): void
    {
        $date = new DateTimeImmutable();

        $stimulant = new Stimulant(
            title: 'NPK',
            manufacturer: 'Complex',
            quantity: 100,
            useDate: $date,
            description: 'Balanced fertilizer'
        );

        $dateNew = new DateTimeImmutable();

        $stimulant->changeFields(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $dateNew,
            description: 'Balanced fertilizer change'
        );

        $this->getProperty($stimulant, 'createdAt', new DateTimeImmutable());
        $this->getProperty($stimulant, 'updatedAt', new DateTimeImmutable());

        $this->assertSame('KNK', $stimulant->getTitle());
        $this->assertSame('Complex change', $stimulant->getManufacturer());
        $this->assertSame('Balanced fertilizer change', $stimulant->getDescription());
        $this->assertSame(50, $stimulant->getQuantity());
        $this->assertSame($dateNew, $stimulant->getUseDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $stimulant->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $stimulant->getUpdatedAt());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $date = new DateTimeImmutable();

        $stimulant = new Stimulant(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change',
        );

        $this->assertSame('KNK', $stimulant->getTitle());
        $this->assertSame('Complex change', $stimulant->getManufacturer());
        $this->assertSame('Balanced fertilizer change', $stimulant->getDescription());
        $this->assertSame(50, $stimulant->getQuantity());
        $this->assertSame($date, $stimulant->getUseDate());
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $date = new DateTimeImmutable();

        $stimulant = new Stimulant(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change'
        );
        $stimulant->getId();
    }

    public function testTouchUpdatesUpdatedAt(): void
    {
        $date = new DateTimeImmutable('2024-01-01');

        $stimulant = new Stimulant(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change');

        $this->getProperty($stimulant, 'updatedAt', new DateTimeImmutable());

        $this->assertNotEquals($date, $stimulant->getUpdatedAt());
    }
}

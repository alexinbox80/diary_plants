<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Pest;
use DateTime;
use PHPUnit\Framework\TestCase;

class PestTest extends TestCase
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
        $date = new DateTime();

        $pest = new Pest(
            'NPK',
            'Complex',
            100,
            $date,
            'Balanced fertilizer'
        );

        $this->getProperty($pest, 'createdAt', new DateTime());
        $this->getProperty($pest, 'updatedAt', new DateTime());

        $this->assertSame('NPK', $pest->getTitle());
        $this->assertSame('Complex', $pest->getManufacturer());
        $this->assertSame('Balanced fertilizer', $pest->getDescription());
        $this->assertSame(100, $pest->getQuantity());
        $this->assertSame($date, $pest->getUseDate());
        $this->assertInstanceOf(DateTime::class, $pest->getCreatedAt());
        $this->assertInstanceOf(DateTime::class, $pest->getUpdatedAt());
    }

    public function testSettersUpdateValuesAndTouchUpdatedAt(): void
    {
        $date = new DateTime();

        $pest = new Pest(
            title: 'NPK',
            manufacturer: 'Complex',
            quantity: 100,
            useDate: $date,
            description: 'Balanced fertilizer'
        );

        $dateNew = new DateTime();

        $pest->changeFields(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $dateNew,
            description: 'Balanced fertilizer change'
        );

        $this->getProperty($pest, 'createdAt', new DateTime());
        $this->getProperty($pest, 'updatedAt', new DateTime());

        $this->assertSame('KNK', $pest->getTitle());
        $this->assertSame('Complex change', $pest->getManufacturer());
        $this->assertSame('Balanced fertilizer change', $pest->getDescription());
        $this->assertSame(50, $pest->getQuantity());
        $this->assertSame($dateNew, $pest->getUseDate());
        $this->assertInstanceOf(DateTime::class, $pest->getCreatedAt());
        $this->assertInstanceOf(DateTime::class, $pest->getUpdatedAt());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $date = new DateTime();

        $pest = new Pest(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change',
        );

        $this->assertSame('KNK', $pest->getTitle());
        $this->assertSame('Complex change', $pest->getManufacturer());
        $this->assertSame('Balanced fertilizer change', $pest->getDescription());
        $this->assertSame(50, $pest->getQuantity());
        $this->assertSame($date, $pest->getUseDate());
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $date = new DateTime();

        $pest = new Pest(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change'
        );
        $pest->getId();
    }

    public function testTouchUpdatesUpdatedAt(): void
    {
        $date = new DateTime('2024-01-01');

        $pest = new Pest(
            title: 'KNK',
            manufacturer: 'Complex change',
            quantity: 50,
            useDate: $date,
            description: 'Balanced fertilizer change');

        $this->getProperty($pest, 'updatedAt', new DateTime());

        $this->assertNotEquals($date, $pest->getUpdatedAt());
    }
}

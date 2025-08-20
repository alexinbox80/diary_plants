<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Status;
use DateTime;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
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
        $status = new Status('K', '#aabbdd');

        $this->getProperty($status, 'createdAt', new DateTime());
        $this->getProperty($status, 'updatedAt', new DateTime());

        $this->assertSame('K', $status->getLetter());
        $this->assertSame('#aabbdd', $status->getColor());
        $this->assertInstanceOf(DateTime::class, $status->getCreatedAt());
        $this->assertInstanceOf(DateTime::class, $status->getUpdatedAt());
    }

    public function testSettersUpdateValues(): void
    {
        $status = new Status('L', '#123456');

        $status->changeFields(
            'L',
            '#123456',
            'Pending task',
            'Light blue for pending'
        );

        $this->assertSame('Pending task', $status->getDescription());
        $this->assertSame('Light blue for pending', $status->getColorDescription());
    }

    public function testToArrayReturnsExpectedArray(): void
    {
        $status = new Status('K', '#aabbdd');
        $this->getProperty($status, 'id', 1);
        $this->getProperty($status, 'description', 'Pending task');
        $this->getProperty($status, 'colorDescription', 'Light blue');
        $this->getProperty($status, 'createdAt', new DateTime('2025-04-01'));
        $this->getProperty($status, 'updatedAt', new DateTime('2025-04-01'));

        $array = $status->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('letter', $array);
        $this->assertArrayHasKey('color', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('color_description', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);

        $this->assertSame(1, $array['id']);
        $this->assertSame('K', $array['letter']);
        $this->assertSame('#aabbdd', $array['color']);
        $this->assertSame('Pending task', $array['description']);
        $this->assertSame('Light blue', $array['color_description']);
        $this->assertSame('2025-04-01 00:00:00', $array['created_at']);
        $this->assertSame('2025-04-01 00:00:00', $array['updated_at']);
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $status = new Status('K', '#aabbdd');
        $status->getId();
    }
}

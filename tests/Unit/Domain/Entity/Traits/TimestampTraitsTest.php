<?php

namespace Unit\Domain\Entity\Traits;

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;

class TimestampTraitsTest extends TestCase
{
    private function createTimestampedObject()
    {
        return new class {
            use CreatedAtTrait, UpdatedAtTrait;
        };
    }

    public function testLifecycleCallbacksSetDates(): void
    {
        $object = $this->createTimestampedObject();

        // Эмулируем PrePersist
        $object->setCreatedAt();
        $object->setUpdatedAt();

        $this->assertInstanceOf(\DateTimeImmutable::class, $object->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $object->getUpdatedAt());

        // Проверяем, что таймзона UTC (как в твоем коде)
        $this->assertEquals('UTC', $object->getCreatedAt()->getTimezone()->getName());
    }
}

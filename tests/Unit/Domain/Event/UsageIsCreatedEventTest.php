<?php

namespace Unit\Domain\Event;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Event\UsageIsCreatedEvent;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UsageIsCreatedEvent::class)]
class UsageIsCreatedEventTest extends TestCase
{
    #[Test]
    public function testEventDataRetained(): void
    {
        // Подготовка данных
        $id = 123;
        $groupId = 2;
        $useDate = new DateTimeImmutable('2024-05-20 10:00:00');
        $plantId = 1;
        $usableId = 10;
        $usableType = 'watering';

        // Создание события
        $event = new UsageIsCreatedEvent(
            $id,
            $groupId,
            $useDate,
            $plantId,
            $usableId,
            $usableType
        );

        // Проверка соответствия
        $this->assertSame($id, $event->id);
        $this->assertSame($groupId, $event->groupId);
        $this->assertSame($useDate, $event->useDate);
        $this->assertSame($plantId, $event->plantId);
        $this->assertSame($usableId, $event->usableId);
        $this->assertSame($usableType, $event->usableType);
    }
}

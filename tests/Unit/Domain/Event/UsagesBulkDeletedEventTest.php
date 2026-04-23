<?php

namespace Unit\Domain\Event;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Event\UsagesBulkDeletedEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

#[CoversClass(UsagesBulkDeletedEvent::class)]
class UsagesBulkDeletedEventTest extends TestCase
{
    #[Test]
    public function testEventDataRetained(): void
    {
        $plantIds = [1, 5, 10, 42];
        $usableType = AttachableType::WATERING->value;

        $event = new UsagesBulkDeletedEvent($plantIds, $usableType);

        $this->assertSame($plantIds, $event->plantIds);
        $this->assertCount(4, $event->plantIds);
        $this->assertSame($usableType, $event->usableType);
    }

    #[Test]
    public function testHandlesEmptyArray(): void
    {
        $event = new UsagesBulkDeletedEvent([], 'test_type');

        $this->assertIsArray($event->plantIds);
        $this->assertEmpty($event->plantIds);
    }
}

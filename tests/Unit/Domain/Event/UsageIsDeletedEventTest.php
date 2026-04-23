<?php

namespace Unit\Domain\Event;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Event\UsageIsDeletedEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

#[CoversClass(UsageIsDeletedEvent::class)]
class UsageIsDeletedEventTest extends TestCase
{
    #[Test]
    public function testEventDataRetained(): void
    {
        $plantId = 42;
        $usableType = AttachableType::WATERING->value;

        $event = new UsageIsDeletedEvent($plantId, $usableType);

        $this->assertSame($plantId, $event->plantId);
        $this->assertSame($usableType, $event->usableType);
    }
}

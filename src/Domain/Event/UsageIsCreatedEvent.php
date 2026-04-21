<?php

namespace App\Domain\Event;

use DateTimeImmutable;

class UsageIsCreatedEvent
{
    public function __construct(
        public readonly int $id,
        public readonly int $groupId,
        public readonly DateTimeImmutable $useDate,
        public readonly int $plantId,
        public readonly int $usableId,
        public readonly string $usableType
    ) {
    }
}

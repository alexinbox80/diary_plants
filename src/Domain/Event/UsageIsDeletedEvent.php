<?php

namespace App\Domain\Event;

class UsageIsDeletedEvent
{
    public function __construct(
        public readonly int $plantId,
        public readonly string $usableType
    ) {}
}

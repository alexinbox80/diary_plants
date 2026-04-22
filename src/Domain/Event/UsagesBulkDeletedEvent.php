<?php

namespace App\Domain\Event;

class UsagesBulkDeletedEvent
{
    public function __construct(
        public readonly array $plantIds,
        public readonly string $usableType
    ) {}
}

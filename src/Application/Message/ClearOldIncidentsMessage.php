<?php

namespace App\Application\Message;

/**
 * Простое сообщение-команда для шины
 */
final readonly class ClearOldIncidentsMessage
{
    public function __construct(
        public int $daysToKeep = 30 // 30 дней по умолчанию
    ) {}
}

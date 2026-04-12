<?php

namespace App\Domain\Service\Csv\Import;

use DateTimeImmutable;
use App\Domain\ValueObject\Price;

abstract class AbstractCsvImporter
{
    protected function dt(?string $v): ?DateTimeImmutable
    {
        // Проверяем и на пустоту, и на null
        return ($v !== null && $v !== '') ? new DateTimeImmutable($v) : null;
    }

    protected function pr(?string $v): ?Price
    {
        return ($v !== null && $v !== '') ? Price::fromString($v) : null;
    }

    protected function str2bool(mixed $v): bool
    {
        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }

    protected function es(?string $string): ?string
    {
        return ($string !== null && $string !== '') ? $string : null;
    }
}

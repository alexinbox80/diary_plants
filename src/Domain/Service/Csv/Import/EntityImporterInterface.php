<?php

namespace App\Domain\Service\Csv\Import;

interface EntityImporterInterface
{
    public function supports(string $fileName): bool;
    public function import(array $data): void;
}

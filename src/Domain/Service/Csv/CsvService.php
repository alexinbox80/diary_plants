<?php

namespace App\Domain\Service\Csv;

use Generator;
use App\Domain\Service\Csv\Import\EntityImporterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class CsvService
{
    private iterable $importers;

    public function __construct(
        private readonly string $csvSeparator,
        #[AutowireIterator('app.entity_importer')] iterable $importers
    ) {
        $this->importers = $importers;
    }

    public function process(string $filePath): int
    {
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        // Отрезаем номер, если файл называется "01.plant.csv"
        $entityName = explode('.', $fileName)[1] ?? $fileName;

        $importer = $this->findImporter($entityName);

        $count = 0;
        foreach ($this->convertCsv($filePath) as $data) {
            $importer->import($data);
            $count++;
        }

        return $count;
    }

    private function findImporter(string $name): EntityImporterInterface
    {
        foreach ($this->importers as $importer) {
            if ($importer->supports($name)) return $importer;
        }
        throw new \Exception("No importer found for $name");
    }

    private function convertCsv(string $filePath): Generator
    {
        $handle = fopen($filePath, 'rb');
        $headers = fgetcsv($handle, 0, $this->csvSeparator);

        while (($row = fgetcsv($handle, 0, $this->csvSeparator)) !== false) {
            // Пропускаем пустые строки
            if ($row === [null] || empty($row)) {
                continue;
            }
            yield array_combine($headers, $row);
        }

        fclose($handle);
    }
}

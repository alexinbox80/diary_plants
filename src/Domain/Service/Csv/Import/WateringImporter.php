<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\ModelFactory;
use App\Domain\Service\WateringService;
use App\Domain\Model\Watering\CreateWateringModel;

class WateringImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly WateringService $wateringService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'watering';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateWateringModel::class, ...$this->map($data));
        $this->wateringService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            (int) $d['marker_id'],
            (int) $d['amount'],
            $d['type'],
            $d['method'],
            $d['temperature'],
            $d['description'],
            $d['comment']
        ];
    }
}

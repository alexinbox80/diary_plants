<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\ModelFactory;
use App\Domain\Service\FertilizerService;
use App\Domain\Model\Fertilizer\CreateFertilizerModel;

class FertilizerImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly FertilizerService $fertilizerService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'fertilizer';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateFertilizerModel::class, ...$this->map($data));
        $this->fertilizerService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            (int) $d['marker_id'],
            $d['title'],
            (int) $d['amount'],
            $d['application_rate'],
            $this->es($d['manufacturer']),
            $this->es($d['description']),
            $this->es($d['comment'])
        ];
    }
}

<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\ModelFactory;
use App\Domain\Service\RepottingService;
use App\Domain\Model\Repotting\CreateRepottingModel;

class RepottingImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly RepottingService $repottingService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'repotting';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateRepottingModel::class, ...$this->map($data));
        $this->repottingService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            (int) $d['plant_id'],
            $this->dt($d['repotted_at']),
            $d['type'],
            $d['pot_material'],
            $d['pot_size'],
            $d['comment']
        ];
    }
}

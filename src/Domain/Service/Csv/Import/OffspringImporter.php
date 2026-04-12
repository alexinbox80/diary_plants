<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\ModelFactory;
use App\Domain\Service\OffspringService;
use App\Domain\Model\Offspring\CreateOffspringModel;

class OffspringImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly OffspringService $offspringService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'offspring';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateOffspringModel::class, ...$this->map($data));
        $this->offspringService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            (int) $d['plant_id'],
            $this->dt($d['fruiting_date']),
            $this->dt($d['flowering_date']),
            (int) $d['mass'],
            $d['color'],
            $d['flavor'],
            (int) $d['quantity'],
            $d['comment']
        ];
    }
}

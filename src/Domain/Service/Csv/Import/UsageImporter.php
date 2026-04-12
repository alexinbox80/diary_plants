<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\ModelFactory;
use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\CreateUsageModel;

class UsageImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly UsageService $usageService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'usage';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateUsageModel::class, ...$this->map($data));
        $this->usageService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            (int) $d['plant_id'],
            $this->dt($d['use_date']),
            (int) $d['usable_id'],
            $d['usable_type'],
            $this->es($d['comment'])
        ];
    }
}

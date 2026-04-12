<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\PestService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\Pest\CreatePestModel;

class PestImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly PestService $pestService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'pest';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreatePestModel::class, ...$this->map($data));
        $this->pestService->create($model);
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

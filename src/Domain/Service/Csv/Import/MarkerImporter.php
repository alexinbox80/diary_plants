<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\ModelFactory;
use App\Domain\Service\MarkerService;
use App\Domain\Model\Marker\CreateMarkerModel;

class MarkerImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly MarkerService $markerService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'marker';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateMarkerModel::class, ...$this->map($data));
        $this->markerService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            $d['letter'],
            $d['color'],
            $d['type'],
            $d['description'],
            $d['color_description']
        ];
    }
}

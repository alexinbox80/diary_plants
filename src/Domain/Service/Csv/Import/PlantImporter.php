<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\PlantService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\Plant\CreatePlantModel;

class PlantImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly PlantService $plantService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'plant';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreatePlantModel::class, ...$this->map($data));
        $this->plantService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            $d['title'],
            $d['room'],
            $this->str2bool($d['is_shown']),
            $d['description'],
            $this->dt($d['purchase_date']),
            $this->dt($d['vaccination_date']),
            $this->dt($d['planting_date']),
            $d['seller'],
            $d['nursery'],
            $this->pr($d['price']),
            $this->pr($d['shipping_cost']),
            $this->pr($d['packaging_cost']),
            $d['soil'],
            $this->str2bool($d['is_sold']),
            $this->dt($d['selling_date']),
            $this->pr($d['selling_price']),
            $d['comment']
        ];
    }
}

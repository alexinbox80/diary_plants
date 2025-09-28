<?php

namespace App\Controller\Web\Admin\Plant\GetPlants;

use App\Domain\Model\Plant\PlantModel;
use App\Domain\Service\PlantService;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService
    ) {
    }

    /**
     * @return array
     */
    public function getPlants(?int $page, ?int $perPage): array
    {
        $plantModels = $this->plantService->getPlantsPaginated($page, $perPage);

        $tableHeader = PlantModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (PlantModel $model): array => $model->toArray(),
            $plantModels
        );

        return [
            'table_header' => $tableHeader,
            'table_body' => $tableBody,
        ];
    }
}

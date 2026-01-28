<?php

namespace App\Controller\Web\Dashboard\Plant\GetPlants;

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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPlants(): array
    {
        $plantsModel = $this->plantService->findAll();
        $tableHeader = PlantModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (PlantModel $model): array => $model->toArray(),
            $plantsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

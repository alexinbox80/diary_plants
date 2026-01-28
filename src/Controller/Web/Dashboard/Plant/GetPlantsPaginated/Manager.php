<?php

namespace App\Controller\Web\Dashboard\Plant\GetPlantsPaginated;

use App\Domain\Model\Plant\PlantModel;
use App\Domain\Service\PlantService;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $plantsModel = $this->plantService->getPlantsPaginated($page, $perPage);
        $tableHeader = PlantModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (PlantModel $model): array => $model->toArray(),
            $plantsModel['plantsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $plantsModel['pagination'],
        ];
    }
}

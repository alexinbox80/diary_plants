<?php

namespace App\Controller\Web\Dashboard\Watering\GetWateringsPaginated;

use App\Domain\Service\WateringService;
use App\Domain\Model\Watering\WateringModel;

class Manager
{
    public function __construct(
        private readonly WateringService $wateringService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getWateringsPaginated(int $page, int $perPage): array
    {
        $wateringsModel = $this->wateringService->getWateringsPaginated($page, $perPage);
        $tableHeader = WateringModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (WateringModel $model): array => $model->toArray(),
            $wateringsModel['wateringsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $wateringsModel['pagination'],
        ];
    }
}

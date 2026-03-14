<?php

namespace App\Controller\Web\Dashboard\Watering\GetWaterings;

use App\Domain\Service\WateringService;
use App\Domain\Model\Watering\WateringModel;

class Manager
{
    public function __construct(
        private readonly WateringService $wateringService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getWaterings(): array
    {
        $wateringsModel = $this->wateringService->findAll();
        $tableHeader = WateringModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (WateringModel $model): array => $model->toArray(),
            $wateringsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Marker\GetMarkers;

use App\Domain\Service\MarkerService;
use App\Domain\Model\Marker\MarkerModel;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMarkers(): array
    {
        $markersModel = $this->markerService->findAll();
        $tableHeader = MarkerModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (MarkerModel $model): array => $model->toArray(),
            $markersModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

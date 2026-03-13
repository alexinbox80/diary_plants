<?php

namespace App\Controller\Web\Dashboard\Marker\GetMarkersPaginated;

use App\Domain\Service\MarkerService;
use App\Domain\Model\Marker\MarkerModel;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMarkersPaginated(int $page, int $perPage): array
    {
        $markersModel = $this->markerService->getMarkerPaginated($page, $perPage);
        $tableHeader = MarkerModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (MarkerModel $model): array => $model->toArray(),
            $markersModel['markersModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $markersModel['pagination'],
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Marker\GetMarkersPaginated;

use App\Domain\Service\MarkerService;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
        private readonly AccessContext $accessContext
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
        $groupId = $this->accessContext->getTargetGroupId();
        $markersModel = $this->markerService->getMarkersPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = MarkerModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (MarkerModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $markersModel['markersModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $markersModel['pagination'],
        ];
    }
}

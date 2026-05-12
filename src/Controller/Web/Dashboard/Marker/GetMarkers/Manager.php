<?php

namespace App\Controller\Web\Dashboard\Marker\GetMarkers;

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
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMarkers(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $markersModel = $this->markerService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();

        $tableHeader = MarkerModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (MarkerModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $markersModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

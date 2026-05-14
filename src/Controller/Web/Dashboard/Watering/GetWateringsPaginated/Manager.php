<?php

namespace App\Controller\Web\Dashboard\Watering\GetWateringsPaginated;

use InvalidArgumentException;
use App\Domain\Service\WateringService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Watering\WateringModel;

final class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getWateringsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $wateringsModel = $this->wateringService->getWateringsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = WateringModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (WateringModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $wateringsModel['wateringsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $wateringsModel['pagination'],
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Repotting\GetRepottingsPaginated;

use App\Domain\Service\RepottingService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Repotting\RepottingModel;

final class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getRepottingsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $repottingsModel = $this->repottingService->getRepottingsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = RepottingModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (RepottingModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $repottingsModel['repottingsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $repottingsModel['pagination'],
        ];
    }
}

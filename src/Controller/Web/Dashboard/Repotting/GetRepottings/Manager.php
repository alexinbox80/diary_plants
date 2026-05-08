<?php

namespace App\Controller\Web\Dashboard\Repotting\GetRepottings;

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
     * @return array
     */
    public function getRepottings(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $repottingsModel = $this->repottingService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();

        $tableHeader = RepottingModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (RepottingModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $repottingsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

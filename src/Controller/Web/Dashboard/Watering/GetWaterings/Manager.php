<?php

namespace App\Controller\Web\Dashboard\Watering\GetWaterings;

use App\Application\Security\AccessContext;
use App\Domain\ValueObject\Enum\Timezone;
use InvalidArgumentException;
use App\Domain\Service\WateringService;
use App\Domain\Model\Watering\WateringModel;

final class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getWaterings(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $wateringsModel = $this->wateringService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = WateringModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (WateringModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $wateringsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Plant\GetPlantsPaginated;

use App\Domain\Service\PlantService;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

final class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $plantsModel = $this->plantService->getPlantsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = PlantModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (PlantModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $plantsModel['plantsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $plantsModel['pagination'],
        ];
    }
}

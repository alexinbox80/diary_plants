<?php

namespace App\Controller\Web\Dashboard\Stimulant\GetStimulantsPaginated;

use InvalidArgumentException;
use App\Domain\Service\StimulantService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Stimulant\StimulantModel;

final class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $stimulantsModel = $this->stimulantService->getStimulantsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = StimulantModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (StimulantModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $stimulantsModel['stimulantsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $stimulantsModel['pagination'],
        ];
    }
}

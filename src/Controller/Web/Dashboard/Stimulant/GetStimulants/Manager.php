<?php

namespace App\Controller\Web\Dashboard\Stimulant\GetStimulants;

use App\Domain\Service\StimulantService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Stimulant\StimulantModel;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getStimulants(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $stimulantsModel = $this->stimulantService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = StimulantModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (StimulantModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $stimulantsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

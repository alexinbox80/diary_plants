<?php

namespace App\Controller\Web\Dashboard\Stimulant\GetStimulantsPaginated;

use App\Domain\Service\StimulantService;
use App\Domain\Model\Stimulant\StimulantModel;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $stimulantsModel = $this->stimulantService->getStimulantsPaginated($page, $perPage);
        $tableHeader = StimulantModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (StimulantModel $model): array => $model->toArray(),
            $stimulantsModel['stimulantsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $stimulantsModel['pagination'],
        ];
    }
}

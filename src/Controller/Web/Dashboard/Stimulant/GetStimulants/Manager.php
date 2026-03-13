<?php

namespace App\Controller\Web\Dashboard\Stimulant\GetStimulants;

use App\Domain\Service\StimulantService;
use App\Domain\Model\Stimulant\StimulantModel;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getStimulants(): array
    {
        $pestsModel = $this->stimulantService->findAll();
        $tableHeader = StimulantModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (StimulantModel $model): array => $model->toArray(),
            $pestsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Repotting\GetRepottingsPaginated;

use App\Domain\Service\RepottingService;
use App\Domain\Model\Repotting\RepottingModel;

class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService
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
        $repottingsModel = $this->repottingService->getRepottingsPaginated($page, $perPage);
        $tableHeader = RepottingModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (RepottingModel $model): array => $model->toArray(),
            $repottingsModel['repottingsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $repottingsModel['pagination'],
        ];
    }
}

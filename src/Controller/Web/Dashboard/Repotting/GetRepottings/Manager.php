<?php

namespace App\Controller\Web\Dashboard\Repotting\GetRepottings;

use App\Domain\Service\RepottingService;
use App\Domain\Model\Repotting\RepottingModel;

class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getRepottings(): array
    {
        $repottingsModel = $this->repottingService->findAll();
        $tableHeader = RepottingModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (RepottingModel $model): array => $model->toArray(),
            $repottingsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

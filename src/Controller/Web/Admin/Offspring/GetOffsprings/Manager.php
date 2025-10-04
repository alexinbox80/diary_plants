<?php

namespace App\Controller\Web\Admin\Offspring\GetOffsprings;

use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Service\OffspringService;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffsprings(): array
    {
        $offspringsModel = $this->offspringService->findAll();
        $tableHeader = OffspringModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (OffspringModel $model): array => $model->toArray(),
            $offspringsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

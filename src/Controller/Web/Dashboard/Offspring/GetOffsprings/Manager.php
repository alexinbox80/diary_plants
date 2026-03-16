<?php

namespace App\Controller\Web\Dashboard\Offspring\GetOffsprings;

use App\Domain\Service\OffspringService;
use App\Domain\Model\Offspring\OffspringModel;

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
        $offspringsModel = $this->offspringService->findAllWithAttachments();
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

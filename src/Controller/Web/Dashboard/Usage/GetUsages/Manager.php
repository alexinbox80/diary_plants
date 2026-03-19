<?php

namespace App\Controller\Web\Dashboard\Usage\GetUsages;

use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getUsages(): array
    {
        $usagesModel = $this->usageService->findAll();
        $tableHeader = UsageModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (UsageModel $model): array => $model->toArray(),
            $usagesModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

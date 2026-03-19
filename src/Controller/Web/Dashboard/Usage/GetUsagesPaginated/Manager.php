<?php

namespace App\Controller\Web\Dashboard\Usage\GetUsagesPaginated;

use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getUsagesPaginated(int $page, int $perPage): array
    {
        $usagesModel = $this->usageService->getUsagesPaginated($page, $perPage);
        $tableHeader = UsageModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (UsageModel $model): array => $model->toArray(),
            $usagesModel['usagesModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $usagesModel['pagination'],
        ];
    }
}

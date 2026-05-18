<?php

namespace App\Controller\Api\Dashboard\Usage\CreateUsage\v1;

use Exception;
use App\Domain\Service\UsageService;
use App\Controller\Api\Dashboard\Usage\CreateUsage\v1\input\CreateUsageDTO;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
    ) {
    }

    /**
     * @param CreateUsageDTO[] $createUsagesDTO
     * @return array
     * @throws Exception
     */
    public function createUsages(array $createUsagesDTO): array
    {
        return $this->usageService->createUsages($createUsagesDTO);
    }
}

<?php

namespace App\Controller\Api\Dashboard\Usage\DeleteUsage\v1;

use App\Domain\Service\UsageService;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
    ) {
    }

    /**
     * @param array $ids
     * @return int
     */
    public function removeByIds(array $ids): int
    {
        return $this->usageService->removeUsages($ids);
    }
}

<?php

namespace App\Controller\Api\Dashboard\Usage\GetUsages\v1;

use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService
    ) {
    }

    /**
     * @return UsageModel[]
     */
    public function getUsages(?int $year = null, ?int $month = null): array
    {
        return $this->usageService->getUsages($year, $month);
    }
}

<?php

namespace App\Controller\Web\Dashboard\Usage\GetUsages;

use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getUsages(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $usagesModel = $this->usageService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = UsageModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (UsageModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $usagesModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

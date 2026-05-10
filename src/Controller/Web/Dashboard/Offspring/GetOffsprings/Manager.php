<?php

namespace App\Controller\Web\Dashboard\Offspring\GetOffsprings;

use App\Domain\Service\OffspringService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Offspring\OffspringModel;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffsprings(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $offspringsModel = $this->offspringService->findAllByGroupId($groupId);

        $tableHeader = OffspringModel::getTableHeaderRu();

        $timezone = $this->accessContext->getTimezone();

        $tableBody = array_map(
            static fn (OffspringModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $offspringsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

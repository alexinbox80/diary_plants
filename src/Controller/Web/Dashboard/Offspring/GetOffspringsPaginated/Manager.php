<?php

namespace App\Controller\Web\Dashboard\Offspring\GetOffspringsPaginated;

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
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $offspringsModel = $this->offspringService->getOffspringsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();

        $tableHeader = OffspringModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (OffspringModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $offspringsModel['offspringsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $offspringsModel['pagination'],
        ];
    }
}

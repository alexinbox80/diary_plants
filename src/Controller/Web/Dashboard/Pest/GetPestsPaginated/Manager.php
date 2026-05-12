<?php

namespace App\Controller\Web\Dashboard\Pest\GetPestsPaginated;

use App\Domain\Service\PestService;
use App\Domain\Model\Pest\PestModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $pestsModel = $this->pestService->getPestsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = PestModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (PestModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $pestsModel['pestsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $pestsModel['pagination'],
        ];
    }
}

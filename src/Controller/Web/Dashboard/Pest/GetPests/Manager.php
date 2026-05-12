<?php

namespace App\Controller\Web\Dashboard\Pest\GetPests;

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
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPests(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $pestsModel = $this->pestService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = PestModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (PestModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $pestsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Fertilizer\GetFertilizersPaginated;

use Psr\Cache\InvalidArgumentException;
use App\Domain\Service\FertilizerService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Fertilizer\FertilizerModel;

final class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $fertilizersModel = $this->fertilizerService->getFertilizersPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = FertilizerModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (FertilizerModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $fertilizersModel['fertilizersModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $fertilizersModel['pagination'],
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Fertilizer\GetFertilizers;

use App\Domain\Service\FertilizerService;
use App\Application\Security\AccessContext;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\ValueObject\Enum\Timezone;

final class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getFertilizers(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $fertilizersModel = $this->fertilizerService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = FertilizerModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (FertilizerModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $fertilizersModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

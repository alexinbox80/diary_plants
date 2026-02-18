<?php

namespace App\Controller\Web\Dashboard\Fertilizer\GetFertilizers;

use App\Domain\Service\FertilizerService;
use App\Domain\Model\Fertilizer\FertilizerModel;

class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getFertilizers(): array
    {
        $fertilizersModel = $this->fertilizerService->findAll();
        $tableHeader = FertilizerModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (FertilizerModel $model): array => $model->toArray(),
            $fertilizersModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Fertilizer\GetFertilizersPaginated;

use App\Domain\Service\FertilizerService;
use App\Domain\Model\Fertilizer\FertilizerModel;

class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
    {
        $fertilizersModel = $this->fertilizerService->getFertilizersPaginated($page, $perPage);
        $tableHeader = FertilizerModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (FertilizerModel $model): array => $model->toArray(),
            $fertilizersModel['fertilizersModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $fertilizersModel['pagination'],
        ];
    }
}

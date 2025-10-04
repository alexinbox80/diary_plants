<?php

namespace App\Controller\Web\Admin\Offspring\GetOffspringsPaginated;

use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Service\OffspringService;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService
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
        $offspringsModel = $this->offspringService->getOffspringsPaginated($page, $perPage);
        $tableHeader = OffspringModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (OffspringModel $model): array => $model->toArray(),
            $offspringsModel['offspringsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $offspringsModel['pagination'],
        ];
    }
}

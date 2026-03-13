<?php

namespace App\Controller\Web\Dashboard\Pest\GetPestsPaginated;

use App\Domain\Service\PestService;
use App\Domain\Model\Pest\PestModel;

class Manager
{
    public function __construct(
        private readonly PestService $pestService
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
        $pestsModel = $this->pestService->getPestsPaginated($page, $perPage);
        $tableHeader = PestModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (PestModel $model): array => $model->toArray(),
            $pestsModel['pestsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $pestsModel['pagination'],
        ];
    }
}

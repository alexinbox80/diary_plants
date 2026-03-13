<?php

namespace App\Controller\Web\Dashboard\Pest\GetPests;

use App\Domain\Service\PestService;
use App\Domain\Model\Pest\PestModel;

class Manager
{
    public function __construct(
        private readonly PestService $pestService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPests(): array
    {
        $pestsModel = $this->pestService->findAll();
        $tableHeader = PestModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (PestModel $model): array => $model->toArray(),
            $pestsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

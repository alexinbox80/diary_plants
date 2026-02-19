<?php

namespace App\Controller\Web\Dashboard\Group\GetGroups;

use App\Domain\Service\GroupService;
use App\Domain\Model\Group\GroupModel;

class Manager
{
    public function __construct(
        private readonly GroupService $groupService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getGroups(): array
    {
        $groupsModel = $this->groupService->findAll();
        $tableHeader = GroupModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (GroupModel $model): array => $model->toArray(),
            $groupsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Group\GetGroupsPaginated;

use App\Domain\Service\GroupService;
use App\Domain\Model\Group\GroupModel;

class Manager
{
    public function __construct(
        private readonly GroupService $groupService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getGroupsPaginated(int $page, int $perPage): array
    {
        $groupsModel = $this->groupService->getGroupsPaginated($page, $perPage);
        $tableHeader = GroupModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (GroupModel $model): array => $model->toArray(),
            $groupsModel['groupsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $groupsModel['pagination'],
        ];
    }
}

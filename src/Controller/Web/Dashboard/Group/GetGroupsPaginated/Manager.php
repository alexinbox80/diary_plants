<?php

namespace App\Controller\Web\Dashboard\Group\GetGroupsPaginated;

use InvalidArgumentException;
use App\Domain\Service\GroupService;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

final class Manager
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getGroupsPaginated(int $page, int $perPage): array
    {
        $timezone = $this->accessContext->getTimezone();

        $groupsModel = $this->groupService->getGroupsPaginated($page, $perPage);
        $tableHeader = GroupModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (GroupModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $groupsModel['groupsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $groupsModel['pagination'],
        ];
    }
}

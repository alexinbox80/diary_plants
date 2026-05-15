<?php

namespace App\Controller\Web\Dashboard\Group\GetGroups;

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
     * @return array
     * @throws InvalidArgumentException
     */
    public function getGroups(): array
    {
        $timezone = $this->accessContext->getTimezone();

        $groupsModel = $this->groupService->findAll();
        $tableHeader = GroupModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (GroupModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $groupsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

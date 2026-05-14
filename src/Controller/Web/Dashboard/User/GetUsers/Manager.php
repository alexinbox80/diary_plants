<?php

namespace App\Controller\Web\Dashboard\User\GetUsers;

use InvalidArgumentException;
use App\Domain\Service\UserService;
use App\Domain\Model\User\UserModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

final class Manager
{
    public function __construct(
        private readonly UserService $userService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getGroups(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $usersModel = $this->userService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = UserModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (UserModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $usersModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\User\GetUsers;

use App\Domain\Service\UserService;
use App\Domain\Model\User\UserModel;

class Manager
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getGroups(): array
    {
        $usersModel = $this->userService->findAll();
        $tableHeader = UserModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (UserModel $model): array => $model->toArray(),
            $usersModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}

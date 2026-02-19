<?php

namespace App\Controller\Web\Dashboard\User\GetUsersPaginated;

use App\Domain\Service\UserService;
use App\Domain\Model\User\UserModel;

class Manager
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getUsersPaginated(int $page, int $perPage): array
    {
        $usersModel = $this->userService->getUsersPaginated($page, $perPage);
        $tableHeader = UserModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (UserModel $model): array => $model->toArray(),
            $usersModel['usersModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $usersModel['pagination'],
        ];
    }
}

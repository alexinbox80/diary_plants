<?php

namespace App\Controller\Web\Dashboard\User\DeleteUser;

use App\Domain\Service\UserService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly UserService $userService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->userService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Пользователь успешно удален.');
        return ['success' => true];
    }
}

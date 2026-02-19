<?php

namespace App\Controller\Web\Dashboard\Group\DeleteGroup;

use App\Domain\Service\GroupService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly GroupService $groupService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->groupService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Группа успешно удалена.');
        return ['success' => true];
    }
}

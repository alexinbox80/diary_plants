<?php

namespace App\Controller\Web\Dashboard\Usage\DeleteUsage;

use App\Domain\Service\UsageService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->usageService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Использование успешно удалено.');
        return ['success' => true];
    }
}

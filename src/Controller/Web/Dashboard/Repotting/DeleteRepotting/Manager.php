<?php

namespace App\Controller\Web\Dashboard\Repotting\DeleteRepotting;

use App\Domain\Service\RepottingService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $this->repottingService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Пересадка успешно удалена.');
        return ['success' => true];
    }
}

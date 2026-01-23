<?php

namespace App\Controller\Web\Admin\Offspring\DeleteOffspring;

use App\Domain\Service\OffspringService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->offspringService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Плод успешно удален.');
        return ['success' => true];
    }
}

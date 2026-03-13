<?php

namespace App\Controller\Web\Dashboard\Pest\DeletePest;

use App\Domain\Service\PestService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->pestService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Вредитель успешно удален.');
        return ['success' => true];
    }
}

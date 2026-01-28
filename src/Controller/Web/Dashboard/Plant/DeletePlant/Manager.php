<?php

namespace App\Controller\Web\Dashboard\Plant\DeletePlant;

use App\Domain\Service\PlantService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->plantService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Растение успешно удалено.');
        return ['success' => true];
    }
}

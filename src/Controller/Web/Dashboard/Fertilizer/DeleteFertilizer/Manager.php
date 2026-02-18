<?php

namespace App\Controller\Web\Dashboard\Fertilizer\DeleteFertilizer;

use App\Domain\Service\FertilizerService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->fertilizerService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Удобрение успешно удалено.');
        return ['success' => true];
    }
}

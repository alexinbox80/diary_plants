<?php

namespace App\Controller\Web\Dashboard\Watering\DeleteWatering;

use App\Domain\Service\WateringService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->wateringService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Полив успешно удален.');
        return ['success' => true];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Marker\DeleteMarker;

use App\Domain\Service\MarkerService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->markerService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Сокращение успешно удалено.');
        return ['success' => true];
    }
}

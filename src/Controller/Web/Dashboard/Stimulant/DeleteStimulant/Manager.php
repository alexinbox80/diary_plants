<?php

namespace App\Controller\Web\Dashboard\Stimulant\DeleteStimulant;

use App\Domain\Service\StimulantService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->stimulantService->removeById($id);

        $request->getSession()->getFlashBag()->add('success', 'Стимулятор успешно удален.');
        return ['success' => true];
    }
}

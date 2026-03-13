<?php

namespace App\Controller\Web\Dashboard\Pest\GetPests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/pests', name: 'dashboard.pests.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $pestsModel = $this->manager->getPests();
        $pests = ['table_header' => $pestsModel['tableHeader'], 'table_body' => $pestsModel['tableBody']];

        return $this->render(
            'dashboard/pest/index.html.twig',
            [
                'pests' => $pests
            ]
        );
    }
}

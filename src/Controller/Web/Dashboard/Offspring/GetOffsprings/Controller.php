<?php

namespace App\Controller\Web\Dashboard\Offspring\GetOffsprings;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/offsprings', name: 'dashboard.offsprings.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $offspringsModel = $this->manager->getOffsprings();
        $offsprings = ['table_header' => $offspringsModel['tableHeader'], 'table_body' => $offspringsModel['tableBody']];

        return $this->render(
            'dashboard/offspring/index.html.twig',
            [
                'offsprings' => $offsprings
            ]
        );
    }
}

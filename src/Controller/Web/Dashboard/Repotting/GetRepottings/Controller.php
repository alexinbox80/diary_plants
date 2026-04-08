<?php

namespace App\Controller\Web\Dashboard\Repotting\GetRepottings;

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

    #[Route(path: '/dashboard/repottings', name: 'dashboard.repottings.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $repottingsModel = $this->manager->getRepottings();
        $repottings = ['table_header' => $repottingsModel['tableHeader'], 'table_body' => $repottingsModel['tableBody']];

        return $this->render(
            'dashboard/repotting/index.html.twig',
            [
                'repottings' => $repottings
            ]
        );
    }
}

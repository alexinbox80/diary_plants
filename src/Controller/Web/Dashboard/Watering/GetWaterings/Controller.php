<?php

namespace App\Controller\Web\Dashboard\Watering\GetWaterings;

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

    #[Route(path: '/dashboard/waterings', name: 'dashboard.waterings.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $wateringsModel = $this->manager->getWaterings();
        $waterings = ['table_header' => $wateringsModel['tableHeader'], 'table_body' => $wateringsModel['tableBody']];

        return $this->render(
            'dashboard/watering/index.html.twig',
            [
                'waterings' => $waterings
            ]
        );
    }
}

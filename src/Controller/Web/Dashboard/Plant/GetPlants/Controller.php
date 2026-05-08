<?php

namespace App\Controller\Web\Dashboard\Plant\GetPlants;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/plants', name: 'dashboard.plants.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $plantsModel = $this->manager->getPlants();
        $plants = ['table_header' => $plantsModel['tableHeader'], 'table_body' => $plantsModel['tableBody']];

        return $this->render(
            'dashboard/plant/index.html.twig',
            [
                'plants' => $plants,
            ]
        );
    }
}

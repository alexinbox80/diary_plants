<?php

namespace App\Controller\Web\Dashboard\Fertilizer\GetFertilizers;

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

    #[Route(path: '/dashboard/fertilizers', name: 'dashboard.fertilizers.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $fertilizersModel = $this->manager->getFertilizers();
        $fertilizers = ['table_header' => $fertilizersModel['tableHeader'], 'table_body' => $fertilizersModel['tableBody']];

        return $this->render(
            'dashboard/fertilizer/index.html.twig',
            [
                'fertilizers' => $fertilizers
            ]
        );
    }
}

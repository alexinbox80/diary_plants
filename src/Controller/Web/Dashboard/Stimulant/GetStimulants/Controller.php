<?php

namespace App\Controller\Web\Dashboard\Stimulant\GetStimulants;

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

    #[Route(path: '/dashboard/stimulants', name: 'dashboard.stimulants.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $stimulantsModel = $this->manager->getStimulants();
        $stimulants = ['table_header' => $stimulantsModel['tableHeader'], 'table_body' => $stimulantsModel['tableBody']];

        return $this->render(
            'dashboard/stimulant/index.html.twig',
            [
                'stimulants' => $stimulants
            ]
        );
    }
}

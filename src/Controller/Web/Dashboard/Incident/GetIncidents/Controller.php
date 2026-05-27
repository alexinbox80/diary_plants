<?php

namespace App\Controller\Web\Dashboard\Incident\GetIncidents;

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

    #[Route(path: '/dashboard/incidents', name: 'dashboard.incidents.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $incidentsModel = $this->manager->getIncidents();
        $incidents = ['table_header' => $incidentsModel['tableHeader'], 'table_body' => $incidentsModel['tableBody']];

        return $this->render(
            'dashboard/incident/index.html.twig',
            [
                'incidents' => $incidents
            ]
        );
    }
}

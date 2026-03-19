<?php

namespace App\Controller\Web\Dashboard\Usage\GetUsages;

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

    #[Route(path: '/dashboard/usages', name: 'dashboard.usages.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $usagesModel = $this->manager->getUsages();
        $usages = ['table_header' => $usagesModel['tableHeader'], 'table_body' => $usagesModel['tableBody']];

        return $this->render(
            'dashboard/usage/index.html.twig',
            [
                'usages' => $usages
            ]
        );
    }
}

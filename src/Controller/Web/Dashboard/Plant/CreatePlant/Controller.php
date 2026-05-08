<?php

namespace App\Controller\Web\Dashboard\Plant\CreatePlant;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/plants/create', name: 'dashboard.plants.create', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $result = $this->manager->createFormData($request);

        if (isset($result['success']) && $result['success'] === true) {

            //перед вызовом контроллера редактирования установить переменную сессии
            //$request->getSession()->set('_previous_route', $request->getRequestUri());

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.plants_paginated.index');
        }

        return $this->render('dashboard/plant/create.html.twig', $result);
    }
}

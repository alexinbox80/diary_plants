<?php

namespace App\Controller\Web\Dashboard\Pest\CreatePest;

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

    #[Route(path: '/dashboard/pests/create', name: 'dashboard.pests.create', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $result = $this->manager->createFormData($request);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.pests_paginated.index');
        }

        return $this->render('dashboard/pest/create.html.twig', $result);
    }
}

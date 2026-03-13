<?php

namespace App\Controller\Web\Dashboard\Pest\EditPest;

use App\Domain\Entity\Pest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/pests/{id}/edit', name: 'dashboard.pests.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Pest $pest): Response
    {
        $result = $this->manager->editFormData($request, $pest);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.pests_paginated.index');
        }

        return $this->render('dashboard/pest/edit.html.twig', $result);
    }
}

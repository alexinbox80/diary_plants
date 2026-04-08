<?php

namespace App\Controller\Web\Dashboard\Repotting\EditRepotting;

use App\Domain\Entity\Repotting;
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

    #[Route(path: '/dashboard/repottings/{id}/edit', name: 'dashboard.repottings.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Repotting $repotting): Response
    {
        $result = $this->manager->editFormData($request, $repotting);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.repottings_paginated.index');
        }

        return $this->render('dashboard/repotting/edit.html.twig', $result);
    }
}

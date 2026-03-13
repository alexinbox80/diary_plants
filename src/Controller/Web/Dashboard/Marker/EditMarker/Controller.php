<?php

namespace App\Controller\Web\Dashboard\Marker\EditMarker;

use App\Domain\Entity\Marker;
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

    #[Route(path: '/dashboard/markers/{id}/edit', name: 'dashboard.markers.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Marker $marker): Response
    {
        $result = $this->manager->editFormData($request, $marker);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.markers_paginated.index');
        }

        return $this->render('dashboard/marker/edit.html.twig', $result);
    }
}

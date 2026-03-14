<?php

namespace App\Controller\Web\Dashboard\Watering\EditWatering;

use App\Domain\Entity\Watering;
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

    #[Route(path: '/dashboard/waterings/{id}/edit', name: 'dashboard.waterings.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Watering $watering): Response
    {
        $result = $this->manager->editFormData($request, $watering);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.waterings_paginated.index');
        }

        return $this->render('dashboard/watering/edit.html.twig', $result);
    }
}

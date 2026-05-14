<?php

namespace App\Controller\Web\Dashboard\Stimulant\EditStimulant;

use App\Domain\Entity\Stimulant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/stimulants/{id}/edit', name: 'dashboard.stimulants.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Stimulant $stimulant): Response
    {
        $result = $this->manager->editFormData($request, $stimulant);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.stimulants_paginated.index');
        }

        return $this->render('dashboard/stimulant/edit.html.twig', $result);
    }
}

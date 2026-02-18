<?php

namespace App\Controller\Web\Dashboard\Fertilizer\EditFertilizer;

use App\Domain\Entity\Fertilizer;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/fertilizers/{id}/edit', name: 'dashboard.fertilizers.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Fertilizer $fertilizer): Response
    {
        $result = $this->manager->editFormData($request, $fertilizer);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.fertilizers_paginated.index');
        }

        return $this->render('dashboard/fertilizer/edit.html.twig', $result);
    }
}

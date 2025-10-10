<?php

namespace App\Controller\Web\Admin\Offspring\EditOffspring;

use App\Domain\Entity\Offspring;
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

    #[Route(path: '/admin/offsprings/{id}/edit', name: 'admin.offsprings.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Offspring $offspring): Response
    {
        $result = $this->manager->editFormData($request, $offspring);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('admin.offsprings_paginated.index');
        }

        return $this->render('admin/offspring/edit.html.twig', $result);
    }
}

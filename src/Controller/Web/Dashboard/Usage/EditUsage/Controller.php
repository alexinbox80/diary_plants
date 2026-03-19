<?php

namespace App\Controller\Web\Dashboard\Usage\EditUsage;

use App\Domain\Entity\Usage;
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

    #[Route(path: '/dashboard/usages/{id}/edit', name: 'dashboard.usages.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Usage $usage): Response
    {
        $result = $this->manager->editFormData($request, $usage);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.usages_paginated.index');
        }

        return $this->render('dashboard/usage/edit.html.twig', $result);
    }
}

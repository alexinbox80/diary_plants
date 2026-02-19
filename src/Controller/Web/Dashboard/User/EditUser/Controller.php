<?php

namespace App\Controller\Web\Dashboard\User\EditUser;

use App\Domain\Entity\User;
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

    #[Route(path: '/dashboard/users/{id}/edit', name: 'dashboard.users.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] User $user): Response
    {
        $result = $this->manager->editFormData($request, $user);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.users_paginated.index');
        }

        return $this->render('dashboard/user/edit.html.twig', $result);
    }
}

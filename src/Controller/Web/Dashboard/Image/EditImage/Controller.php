<?php

namespace App\Controller\Web\Dashboard\Image\EditImage;

use App\Domain\Entity\Attachment;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/images/{id}/edit', name: 'dashboard.images.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Attachment $attachment): Response
    {
        $result = $this->manager->editFormData($request, $attachment);

        if (isset($result['success']) && $result['success'] === true) {

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('dashboard.images_paginated.index');
        }

        return $this->render('dashboard/image/edit.html.twig', $result);
    }
}

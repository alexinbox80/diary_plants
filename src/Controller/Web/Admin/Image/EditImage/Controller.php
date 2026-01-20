<?php

namespace App\Controller\Web\Admin\Image\EditImage;

use App\Domain\Entity\Plant;
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

    #[Route(path: '/admin/images/{id}/edit', name: 'admin.images.edit', methods: ['GET', 'PATCH'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Plant $plant): Response
    {
        $result = $this->manager->editFormData($request, $plant);

        if (isset($result['success']) && $result['success'] === true) {

            //перед вызовом контроллера редактирования установить переменную сессии
            //$request->getSession()->set('_previous_route', $request->getRequestUri());

            $previousRoute = $request->getSession()->get('_previous_route');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            }

            return $this->redirectToRoute('admin.images_paginated.index');
        }

        return $this->render('admin/image/edit.html.twig', $result);
    }
}

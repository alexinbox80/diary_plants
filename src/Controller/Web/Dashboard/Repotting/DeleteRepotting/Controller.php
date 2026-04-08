<?php

namespace App\Controller\Web\Dashboard\Repotting\DeleteRepotting;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/repottings/{id}/delete', name: 'dashboard.repottings.delete', methods: ['DELETE'])]
    public function __invoke(int $id, Request $request): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            //return $this->redirectToRoute('admin.images_paginated.index');
            throw new InvalidCsrfTokenException();
        }

        $result = $this->manager->deleteData($id, $request);

        if (isset($result['success']) && $result['success'] === true) {

            return $this->redirectToRoute('dashboard.repottings_paginated.index');
        }

        return $this->redirectToRoute('dashboard.repottings_paginated.index');
    }
}

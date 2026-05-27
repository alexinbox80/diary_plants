<?php

namespace App\Controller\Web\Dashboard\Incident\DeleteIncident;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/incidents/{id}/delete', name: 'dashboard.incidents.delete', methods: ['DELETE'])]
    public function __invoke(int $id, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException();
        }

        $result = $this->manager->deleteData($id, $request);

        if (isset($result['success']) && $result['success'] === true) {
            return $this->redirectToRoute('dashboard.incidents_paginated.index');
        }

        return $this->redirectToRoute('dashboard.incidents_paginated.index');
    }
}

<?php

namespace App\Controller\Web\Admin\Plant\GetPlantsPaginated;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/admin/plants-paginated', name: 'admin.plants_paginated.index', methods: ['GET'])]
    public function __invoke(): Response
    {
        $itemsPerPage = $this->getParameter('items_per_page');
        $plantsModel = $this->manager->getPlantsPaginated($page ?? 1, $perPage ?? $itemsPerPage);
        $plants = ['table_header' => $plantsModel['tableHeader'], 'table_body' => $plantsModel['tableBody']];

        return $this->render(
            'admin/plant/index.html.twig',
            [
                'plants' => $plants,
                'pagination' => $plantsModel['pagination']
            ]
        );
    }
}

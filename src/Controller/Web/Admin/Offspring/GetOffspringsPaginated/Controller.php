<?php

namespace App\Controller\Web\Admin\Offspring\GetOffspringsPaginated;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: '/admin/offsprings-paginated',
        name: 'admin.offsprings_paginated.index',
        requirements: ['page' => '\d+', 'per_page' => '\d+'],
        methods: ['GET']
    )]
    public function __invoke(
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] ?int $page = null,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] ?int $per_page = null,
    ): Response
    {
        $itemsPerPage = $this->getParameter('items_per_page');
        $offspringsModel = $this->manager->getOffspringsPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $offsprings = ['table_header' => $offspringsModel['tableHeader'], 'table_body' => $offspringsModel['tableBody']];

        return $this->render(
            'admin/offspring/index.html.twig',
            [
                'offsprings' => $offsprings,
                'pagination' => $offspringsModel['pagination']
            ]
        );
    }
}

<?php

namespace App\Controller\Web\Dashboard\Watering\GetWateringsPaginated;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: '/dashboard/waterings-paginated',
        name: 'dashboard.waterings_paginated.index',
        requirements: ['page' => '\d+', 'per_page' => '\d+'],
        methods: ['GET']
    )]
    public function __invoke(
        Request $request,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] ?int $page = null,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] ?int $per_page = null,
    ): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $itemsPerPage = $this->getParameter('items_per_page');
        $wateringsModel = $this->manager->getWateringsPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $waterings = ['table_header' => $wateringsModel['tableHeader'], 'table_body' => $wateringsModel['tableBody']];

        return $this->render(
            'dashboard/watering/index.html.twig',
            [
                'waterings' => $waterings,
                'pagination' => $wateringsModel['pagination']
            ]
        );
    }
}

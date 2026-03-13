<?php

namespace App\Controller\Web\Dashboard\Marker\GetMarkersPaginated;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: '/dashboard/markers-paginated',
        name: 'dashboard.markers_paginated.index',
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
        $markersModel = $this->manager->getMarkersPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $markers = ['table_header' => $markersModel['tableHeader'], 'table_body' => $markersModel['tableBody']];

        return $this->render(
            'dashboard/marker/index.html.twig',
            [
                'markers' => $markers,
                'pagination' => $markersModel['pagination']
            ]
        );
    }
}

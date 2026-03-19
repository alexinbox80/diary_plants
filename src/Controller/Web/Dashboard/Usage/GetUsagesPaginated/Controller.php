<?php

namespace App\Controller\Web\Dashboard\Usage\GetUsagesPaginated;

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
        path: '/dashboard/usages-paginated',
        name: 'dashboard.usages_paginated.index',
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
        $usagesModel = $this->manager->getUsagesPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $usages = ['table_header' => $usagesModel['tableHeader'], 'table_body' => $usagesModel['tableBody']];

        return $this->render(
            'dashboard/usage/index.html.twig',
            [
                'usages' => $usages,
                'pagination' => $usagesModel['pagination']
            ]
        );
    }
}

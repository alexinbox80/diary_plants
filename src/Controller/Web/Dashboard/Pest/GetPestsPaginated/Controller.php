<?php

namespace App\Controller\Web\Dashboard\Pest\GetPestsPaginated;

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
        path: '/dashboard/pests-paginated',
        name: 'dashboard.pests_paginated.index',
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
        $pestsModel = $this->manager->getPestsPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $pests = ['table_header' => $pestsModel['tableHeader'], 'table_body' => $pestsModel['tableBody']];

        return $this->render(
            'dashboard/pest/index.html.twig',
            [
                'pests' => $pests,
                'pagination' => $pestsModel['pagination']
            ]
        );
    }
}

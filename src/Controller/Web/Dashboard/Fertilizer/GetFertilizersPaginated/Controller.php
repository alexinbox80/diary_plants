<?php

namespace App\Controller\Web\Dashboard\Fertilizer\GetFertilizersPaginated;

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
        path: '/dashboard/fertilizers-paginated',
        name: 'dashboard.fertilizers_paginated.index',
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
        $fertilizersModel = $this->manager->getFertilizersPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $fertilizers = ['table_header' => $fertilizersModel['tableHeader'], 'table_body' => $fertilizersModel['tableBody']];

        return $this->render(
            'dashboard/fertilizer/index.html.twig',
            [
                'fertilizers' => $fertilizers,
                'pagination' => $fertilizersModel['pagination']
            ]
        );
    }
}

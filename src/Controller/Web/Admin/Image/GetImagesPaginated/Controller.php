<?php

namespace App\Controller\Web\Admin\Image\GetImagesPaginated;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        path: '/admin/images-paginated',
        name: 'admin.images_paginated.index',
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
        $plantsModel = $this->manager->getPlantsPaginated($page ?? 1, $per_page ?? $itemsPerPage);
        $plants = ['table_header' => $plantsModel['tableHeader'], 'table_body' => $plantsModel['tableBody']];

        return $this->render(
            'admin/image/index.html.twig',
            [
                'images' => $plants,
                'pagination' => $plantsModel['pagination']
            ]
        );
    }
}

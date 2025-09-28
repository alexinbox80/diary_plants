<?php

namespace App\Controller\Web\Admin\Plant\GetPlants;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/admin/plant', name: 'admin.plant.index', methods: ['GET'])]
    public function __invoke(): Response
    {
        $itemsPerPage = $this->getParameter('items_per_page');
        $plants = $this->manager->getPlants($page ?? 0, $perPage ?? $itemsPerPage);
        $pages = [
            'first' => 1,
            'last' => $itemsPerPage,
            'total' => $plants['plantsCount'],
        ];
        $plants = ['table_header' => $plants['tableHeader'], 'table_body' => $plants['tableBody']];

        return $this->render('admin/plant/index.html.twig', ['plants' => $plants, 'pages' => $pages]);
    }
}

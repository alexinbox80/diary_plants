<?php

namespace App\Controller\Web\Admin\Offspring\GetOffsprings;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/admin/offsprings', name: 'admin.offsprings.index', methods: ['GET'])]
    public function __invoke(): Response
    {
        $offspringsModel = $this->manager->getOffsprings();
        $offsprings = ['table_header' => $offspringsModel['tableHeader'], 'table_body' => $offspringsModel['tableBody']];

        return $this->render(
            'admin/offspring/index.html.twig',
            [
                'offsprings' => $offsprings
            ]
        );
    }
}

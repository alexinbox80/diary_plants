<?php

namespace App\Controller\Api\Dashboard\Usage\GetUsages\v1;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/dashboard/usages/{year}/{month}',
        name: 'api.v1.dashboard.usages.index',
        requirements: [
            'year' => '\d{4}',
            'month' => '0?[1-9]|1[0-2]'
        ],
        defaults: [
            'year' => null,
            'month' => null
        ],
        methods: ['GET']
    )]
    public function __invoke(
        ?int $year = null, ?int $month = null
    ): JsonResponse
    {
        return $this->json(['data' => $this->manager->getUsages($year, $month)]);
    }
}

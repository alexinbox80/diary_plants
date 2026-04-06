<?php

namespace App\Controller\Api\Dashboard\Usage\CreateUsage\v1;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\Api\Dashboard\Usage\CreateUsage\v1\input\CreateUsageDTO;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/dashboard/usages/create',
        name: 'api.v1.dashboard.usages.create',

        methods: ['POST']
    )]    public function __invoke(
        /** @var CreateUsageDTO[] $createUsagesDTO */
        #[MapRequestPayload(type: CreateUsageDTO::class)] array $createUsagesDTO
    ): JsonResponse
    {
        $results = $this->manager->createUsages($createUsagesDTO);

        if ($results) {
            return $this->json(['status' => 'success', 'data' => $results], Response::HTTP_CREATED);
        } else {
            return $this->json(['status' => 'error'], Response::HTTP_BAD_REQUEST);
        }
    }
}

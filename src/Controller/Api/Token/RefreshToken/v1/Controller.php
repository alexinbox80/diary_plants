<?php

namespace App\Controller\Api\Token\RefreshToken\v1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/refresh-token',
        name: 'api.v1.refresh_token',
        methods: ['POST']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data =  ['token' => $this->manager->refreshToken($this->getUser())];

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}

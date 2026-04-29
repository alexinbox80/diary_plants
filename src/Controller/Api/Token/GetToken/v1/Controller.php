<?php

namespace App\Controller\Api\Token\GetToken\v1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class Controller
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/get-token',
        name: 'api.v1.get_token',
        methods: ['POST']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data =  ['token' => $this->manager->getToken($request)];

        return new JsonResponse($data, Response::HTTP_OK, [], false);
    }
}

<?php

namespace App\Controller\Api\Dashboard\Usage\DeleteUsage\v1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: 'api/v1/dashboard/usages/remove', name: 'api.v1.dashboard.usages.delete', methods: ['DELETE'])]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            return $this->json(['status' => 'error', 'message' => 'IDs are required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $total = $this->manager->removeByIds($ids);
            return $this->json(['status' => 'success', 'total' => $total], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Controller\Web\Dashboard\Plant\ProfilePlant;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: '/dashboard/plants/{uuid}/profile',
        name: 'dashboard.plants.profile',
        requirements: ['uuid' => Requirement::UUID_V4],
        methods: ['GET']
    )]
    public function __invoke(Request $request, string $uuid): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $plantModel = $this->manager->getPlantProfile($uuid);

        $plant = ['profile_header' => $plantModel['profileHeader'], 'profile_body' => $plantModel['profileBody']];

        return $this->render(
            'dashboard/plant/profile.html.twig',
            [
                'plant' => $plant,
            ]
        );
    }
}

<?php

namespace App\Controller\Web\Dashboard\User\GetUsers;

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

    #[Route(path: '/dashboard/users', name: 'dashboard.users.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $groupsModel = $this->manager->getGroups();
        $users = ['table_header' => $groupsModel['tableHeader'], 'table_body' => $groupsModel['tableBody']];

        return $this->render(
            'dashboard/user/index.html.twig',
            [
                'users' => $users
            ]
        );
    }
}

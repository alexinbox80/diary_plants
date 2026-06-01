<?php

namespace App\Controller\Web\Dashboard\UserMessage\GetUserMessages;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/user-messages', name: 'dashboard.user-messages.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $userMessagesModel = $this->manager->getUserMessages();
        $userMessages = ['table_header' => $userMessagesModel['tableHeader'], 'table_body' => $userMessagesModel['tableBody']];

        return $this->render(
            'dashboard/userMessage/index.html.twig',
            [
                'user-messages' => $userMessages
            ]
        );
    }
}

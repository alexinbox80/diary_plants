<?php

namespace App\Controller\Web\Dashboard\User\CreateUser;

use App\Controller\Form\UserType;
use App\Domain\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\User\CreateUser\Input\CreateUserDTO;

class Manager
{
    public function __construct(
        private readonly UserService $groupService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(UserType::class, null, ['is_new' => $isNew, 'group_id' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateUserDTO $createUserDTO */
            $createUserDTO = $form->getData();

            $this->groupService->createFromCreateUserDTO($createUserDTO);

            $request->getSession()->getFlashBag()->add('success', 'Пользователь успешно создан.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

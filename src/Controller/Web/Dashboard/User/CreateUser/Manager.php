<?php

namespace App\Controller\Web\Dashboard\User\CreateUser;

use App\Controller\Form\UserType;
use App\Domain\Service\UserService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\User\CreateUser\Input\CreateUserDTO;

final class Manager
{
    public function __construct(
        private readonly UserService $groupService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(UserType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateUserDTO $createUserDTO */
            $createUserDTO = $form->getData();

            if (!$createUserDTO->groupId) {
                $createUserDTO->groupId = $groupId;
            }

            $this->groupService->createFromCreateUserDTO($createUserDTO);

            $message = $this->translator->trans('user.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

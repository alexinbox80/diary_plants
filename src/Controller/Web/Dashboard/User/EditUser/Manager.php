<?php

namespace App\Controller\Web\Dashboard\User\EditUser;

use App\Domain\Entity\User;
use App\Controller\Form\UserType;
use App\Domain\Service\UserService;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\User\EditUser\Input\EditUserDTO;

class Manager
{
    public function __construct(
        private readonly UserService $userService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, User $user): array
    {
        $formData = new EditUserDTO(
            $user->getGroup()->getId(),
            $user->getEmail(),
            $user->getPassword(),
            UserRole::toString($user->getRoles()),
            $user->isActive(),
            $user->isEmailConfirmed(),
            $user->isPhoneConfirmed(),
            $user->getTimeZone(),
            $user->getName()->getLast(),
            $user->getName()->getFirst(),
            $user->getName()->getMiddle(),
            $user->getRefreshToken(),
            $user->getPhone(),
            $user->getAvatarLink(),
            $user->getEmailCode(),
            $user->getPhoneCode()
        );

        $form = $this->formFactory->create(UserType::class, $formData, ['group_id' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditUserDTO $editUserDTO */
            $editUserDTO = $form->getData();

            $data = $request->request->all()['user'] ?? [];
            $editUserDTO->isActive = (bool) ($data['isActive'] ?? false);
            $editUserDTO->emailConfirmed = (bool) ($data['emailConfirmed'] ?? false);
            $editUserDTO->phoneConfirmed = (bool) ($data['phoneConfirmed'] ?? false);

            $this->userService->updateFromEditUserDTO($user, $editUserDTO);

            $request->getSession()->getFlashBag()->add('success', 'Пользователь успешно обновлен.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'user' => $user
        ];
    }
}

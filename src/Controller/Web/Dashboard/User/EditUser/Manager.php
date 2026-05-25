<?php

namespace App\Controller\Web\Dashboard\User\EditUser;

use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Domain\Entity\User;
use App\Controller\Form\UserType;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Service\UserService;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\User\EditUser\Input\EditUserDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly UserService $userService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, User $user): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $user)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $user->getGroup()->getId();

        $formData = new EditUserDTO(
            groupId: $groupId,
            email: $user->getEmail(),
            password: $user->getPassword(),
            roles: UserRole::toString($user->getRoles()),
            isActive: $user->isActive(),
            emailConfirmed: $user->isEmailConfirmed(),
            phoneConfirmed: $user->isPhoneConfirmed(),
            timeZone: $user->getTimeZone(),
            lastName: $user->getName()->getLast(),
            firstName: $user->getName()->getFirst(),
            middleName: $user->getName()->getMiddle(),
            refreshToken: $user->getRefreshToken()->getToken(),
            phone: $user->getPhone(),
            avatarLink: $user->getAvatarLink(),
            emailCode: $user->getEmailCode(),
            phoneCode: $user->getPhoneCode()
        );

        $form = $this->formFactory->create(UserType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditUserDTO $editUserDTO */
            $editUserDTO = $form->getData();

            if (!$editUserDTO->groupId) {
                $editUserDTO->groupId = $groupId;
            }

            $data = $request->request->all()['user'] ?? [];
            $editUserDTO->isActive = (bool) ($data['isActive'] ?? false);
            $editUserDTO->emailConfirmed = (bool) ($data['emailConfirmed'] ?? false);
            $editUserDTO->phoneConfirmed = (bool) ($data['phoneConfirmed'] ?? false);

            $this->userService->updateFromEditUserDTO($user, $editUserDTO);

            $message = $this->translator->trans('user.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'user' => $user
        ];
    }
}

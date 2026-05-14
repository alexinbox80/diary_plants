<?php

namespace App\Controller\Web\Dashboard\User\DeleteUser;

use App\Domain\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly UserService $userService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $user = $this->userService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $user)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->userService->deleteWithFile($id);

        $message = $this->translator->trans('user.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);

        return ['success' => true];
    }
}

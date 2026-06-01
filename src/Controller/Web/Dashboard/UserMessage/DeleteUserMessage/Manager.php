<?php

namespace App\Controller\Web\Dashboard\UserMessage\DeleteUserMessage;

use App\Domain\Service\UserMessageService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly UserMessageService $userMessageService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $userMessage = $this->userMessageService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $userMessage)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->userMessageService->removeById($id);

        $message = $this->translator->trans('user-message.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

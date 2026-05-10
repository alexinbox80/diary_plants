<?php

namespace App\Controller\Web\Dashboard\Usage\DeleteUsage;

use App\Domain\Service\UsageService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $usage = $this->usageService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $usage)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->usageService->removeById($id);

        $message = $this->translator->trans('usage.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

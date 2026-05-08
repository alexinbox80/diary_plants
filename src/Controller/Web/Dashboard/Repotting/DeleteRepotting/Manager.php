<?php

namespace App\Controller\Web\Dashboard\Repotting\DeleteRepotting;

use App\Domain\Service\RepottingService;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $repotting = $this->repottingService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $repotting)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }
        $this->repottingService->removeById($id);

        $message = $this->translator->trans('repotting.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);

        return ['success' => true];
    }
}

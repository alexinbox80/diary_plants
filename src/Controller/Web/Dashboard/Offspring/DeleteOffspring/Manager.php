<?php

namespace App\Controller\Web\Dashboard\Offspring\DeleteOffspring;

use App\Domain\Service\OffspringService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $offspring = $this->offspringService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $offspring)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->offspringService->removeById($id);

        $message = $this->translator->trans('offspring.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

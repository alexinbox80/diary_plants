<?php

namespace App\Controller\Web\Dashboard\Stimulant\DeleteStimulant;

use App\Domain\Service\StimulantService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $stimulant = $this->stimulantService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $stimulant)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->stimulantService->removeById($id);

        $message = $this->translator->trans('stimulant.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);

        return ['success' => true];
    }
}

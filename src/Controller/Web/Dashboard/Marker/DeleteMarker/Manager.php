<?php

namespace App\Controller\Web\Dashboard\Marker\DeleteMarker;

use App\Domain\Service\MarkerService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $marker = $this->markerService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $marker)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->markerService->removeById($id);

        $message = $this->translator->trans('marker.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Watering\DeleteWatering;

use App\Domain\Service\WateringService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $watering = $this->wateringService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $watering)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->wateringService->removeById($id);

        $message = $this->translator->trans('watering.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Fertilizer\DeleteFertilizer;

use App\Domain\Service\FertilizerService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $fertilizer = $this->fertilizerService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $fertilizer)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->fertilizerService->removeById($id);

        $message = $this->translator->trans('fertilizer.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

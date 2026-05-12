<?php

namespace App\Controller\Web\Dashboard\Pest\DeletePest;

use App\Domain\Service\PestService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $pest = $this->pestService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $pest)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }

        $this->pestService->removeById($id);

        $message = $this->translator->trans('pest.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

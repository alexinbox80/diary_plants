<?php

namespace App\Controller\Web\Dashboard\Image\DeleteImage;

use App\Domain\Service\AttachmentService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $attachment = $this->attachmentService->find($id);
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::DELETE, $attachment)) {
            $message = $this->translator->trans('security.access_denied.delete');
            throw new AccessDeniedException($message);
        }
        $this->attachmentService->deleteWithFile($id);

        $message = $this->translator->trans('image.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);
        return ['success' => true];
    }
}

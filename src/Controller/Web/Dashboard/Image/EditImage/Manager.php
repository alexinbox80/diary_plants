<?php

namespace App\Controller\Web\Dashboard\Image\EditImage;

use App\Domain\Entity\Attachment;
use App\Controller\Form\ImageType;
use App\Domain\Service\AttachmentService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Image\EditImage\Input\EditImageDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Attachment $attachment): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $attachment)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $attachment->getGroup()->getId();

        $formData = new EditImageDTO(
            $groupId,
            $attachment->getDisplaySettings()->isShown(),
            $attachment->getFileInfo()->getFilename(),
            $attachment->getFileInfo()->getPath(),
            $attachment->getFileInfo()->getMimeType(),
            $attachment->getDisplaySettings()->getAlt(),
            $attachment->getDisplaySettings()->getTitle(),
            $attachment->getFileInfo()->getFileDate(),
            $attachment->getDisplaySettings()->getDescription(),
            (int) $attachment->getTarget()->getAttachableId(),
            $attachment->getTarget()->getAttachableType()->value,
            null
        );

        $form = $this->formFactory->create(ImageType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditImageDTO $editImageDTO */
            $editImageDTO = $form->getData();

            if (!$editImageDTO->groupId) {
                $editImageDTO->groupId = $groupId;
            }

            $data = $request->request->all()['image'] ?? [];
            $editImageDTO->isShown = (bool) ($data['isShown'] ?? false);

            $this->attachmentService->updateFromEditImageDTO($attachment, $editImageDTO);

            $message = $this->translator->trans('image.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form->createView(),
            'image' => $attachment
        ];
    }
}

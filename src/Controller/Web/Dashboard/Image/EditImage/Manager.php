<?php

namespace App\Controller\Web\Dashboard\Image\EditImage;

use App\Domain\Entity\Attachment;
use App\Controller\Form\ImageType;
use App\Domain\Service\AttachmentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Image\EditImage\Input\EditImageDTO;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function editFormData(Request $request, Attachment $attachment): array
    {
        $formData = new EditImageDTO(
            $attachment->getDisplaySettings()->isShown(),
            $attachment->getFileInfo()->getFilename(),
            $attachment->getFileInfo()->getPath(),
            $attachment->getFileInfo()->getMimeType(),
            $attachment->getDisplaySettings()->getAlt(),
            $attachment->getDisplaySettings()->getTitle(),
            $attachment->getFileInfo()->getFileDate(),
            $attachment->getDisplaySettings()->getDescription(),
            (int) $attachment->getTarget()->getAttachableId(),
            $attachment->getTarget()->getAttachableType(),
            null
        );

        $form = $this->formFactory->create(ImageType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditImageDTO $editImageDTO */
            $editImageDTO = $form->getData();

            $data = $request->request->all()['image'] ?? [];
            $editImageDTO->isShown = (bool) ($data['isShown'] ?? false);

            $this->attachmentService->updateFromEditImageDTO($attachment, $editImageDTO);

            $request->getSession()->getFlashBag()->add('success', 'Изображение успешно обновлено.');
            return ['success' => true];
        }

        return [
            'form' => $form->createView(),
            'image' => $attachment
        ];
    }
}

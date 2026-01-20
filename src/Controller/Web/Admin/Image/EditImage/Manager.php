<?php

namespace App\Controller\Web\Admin\Image\EditImage;

use App\Controller\Form\ImageType;
use App\Controller\Web\Admin\Image\EditImage\Input\EditImageDTO;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\UpdateAttachmentModel;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\AttachmentService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory,
        private readonly ModelFactory $modelFactory
    ) {
    }

    public function editFormData(Request $request, Attachment $attachment): array
    {

        $formData = new EditImageDTO(
            $attachment->getFilename(),
            $attachment->getPath(),
            $attachment->getMimeType(),
            $attachment->getAlt(),
            $attachment->getTitle(),
            $attachment->getFileDate(),
            $attachment->getDescription(),
            $attachment->getAttachableId(),
            $attachment->getAttachableType()
        );

        $form = $this->formFactory->create(ImageType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditImageDTO $editImageDTO */
            $editImageDTO = $form->getData();

            $updateAttachmentModel = $this->modelFactory->makeModel(
                UpdateAttachmentModel::class,
                $editImageDTO->filename,
                $editImageDTO->path,
                $editImageDTO->mimeType,
                $editImageDTO->alt,
                $editImageDTO->title,
                $editImageDTO->fileDate,
                $editImageDTO->attachableId,
                $editImageDTO->attachableType,
                $editImageDTO->description,
            );

            $this->attachmentService->update($attachment, $updateAttachmentModel);

            $request->getSession()->getFlashBag()->add('success', 'Изображение успешно обновлено.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'image' => $attachment
        ];
    }
}

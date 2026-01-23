<?php

namespace App\Controller\Web\Admin\Image\EditImage;

use App\Controller\Form\ImageType;
use App\Controller\Web\Admin\Image\EditImage\Input\EditImageDTO;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\UpdateAttachmentModel;
use App\Domain\Service\FileService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\AttachmentService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory,
        private readonly ModelFactory $modelFactory,
        private readonly FileService $fileService,
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
            (int) $attachment->getAttachableId(),
            $attachment->getAttachableType(),
            null
        );

        $form = $this->formFactory->create(ImageType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditImageDTO $editImageDTO */
            $editImageDTO = $form->getData();

            if ($editImageDTO->imageFile instanceof UploadedFile) {
                // удалить старый файл
                if (!is_null($editImageDTO->path) || !is_null($editImageDTO->filename))
                    $this->fileService->removeUploadedFile($editImageDTO->path . $editImageDTO->filename);

                // обновить данные
                $editImageDTO->path = $this->fileService->getAttachmentsPath($editImageDTO->attachableType, $editImageDTO->attachableId);
                $editImageDTO->mimeType = $editImageDTO->imageFile->getMimeType();

                $uploadFile = $this->fileService->storeUploadedFile($editImageDTO->imageFile, $editImageDTO->path);
                $editImageDTO->filename = $uploadFile->getFilename();
            }

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

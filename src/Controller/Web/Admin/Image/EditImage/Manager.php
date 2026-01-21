<?php

namespace App\Controller\Web\Admin\Image\EditImage;

use App\Controller\Form\ImageType;
use App\Controller\Web\Admin\Image\EditImage\Input\EditImageDTO;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\UpdateAttachmentModel;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\AttachmentService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    private string $uploadDirectory;

    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory,
        private readonly ModelFactory $modelFactory,
        string $uploadDirectory
    ) {
        $this->uploadDirectory = $uploadDirectory;
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

            // Обработка загрузки нового изображения (берём из $request через UploadedFile)
            if ($editImageDTO->imageFile instanceof UploadedFile) {
                //$originalFilename = pathinfo($editImageDTO->imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $fileName = sprintf('%s.%s', uniqid('image', true), $editImageDTO->imageFile->getClientOriginalExtension());

                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                //$fileName = $safeFilename . '-' . uniqid() . '.' . $editImageDTO->imageFile->guessExtension();

                // Создаем директорию, если она не существует
                $directory = $this->uploadDirectory . '/attachments/plant/' . $editImageDTO->attachableId;
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Обновляем данные в DTO — теперь используем новое имя и путь
                $editImageDTO->filename = $fileName;
                $editImageDTO->path = 'attachments/plant/' . $editImageDTO->attachableId . '/';
                $editImageDTO->mimeType = $editImageDTO->imageFile->getMimeType();

                // Перемещаем файл в директорию
                $editImageDTO->imageFile->move($directory, $fileName);
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
            //'form' => $form,
            'form' => $form->createView(),
            'image' => $attachment
        ];
    }
}

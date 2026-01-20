<?php

namespace App\Controller\Web\Admin\Image\CreateImage;

use App\Controller\Form\ImageType;
use App\Controller\Web\Admin\Image\CreateImage\Input\CreateImageDTO;
use App\Domain\Model\Attachment\CreateAttachmentModel;
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

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(ImageType::class, null, ['isNew' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateImageDTO $createImageDTO */
            $createImageDTO = $form->getData();

            $createImageModel = $this->modelFactory->makeModel(
                CreateAttachmentModel::class,
                $createImageDTO->filename,
                $createImageDTO->path,
                $createImageDTO->mimeType,
                $createImageDTO->alt,
                $createImageDTO->title,
                $createImageDTO->fileDate,
                $createImageDTO->attachableId,
                $createImageDTO->attachableType,
                $createImageDTO->description,
            );

            $imageModel = $this->attachmentService->create($createImageModel);

            $request->getSession()->getFlashBag()->add('success', 'Изображение успешно сохранено.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'isNew' => $isNew,
        ];
    }
}

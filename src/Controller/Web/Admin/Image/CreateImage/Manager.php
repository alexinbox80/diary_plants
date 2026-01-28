<?php

namespace App\Controller\Web\Admin\Image\CreateImage;

use App\Controller\Form\ImageType;
use App\Domain\Service\AttachmentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Admin\Image\CreateImage\Input\CreateImageDTO;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory
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

            $this->attachmentService->createFromCreateImageDTO($createImageDTO);

            $request->getSession()->getFlashBag()->add('success', 'Изображение успешно сохранено.');
            return ['success' => true];
        }

        return [
            'form' => $form->createView(),
            'isNew' => $isNew,
        ];
    }
}

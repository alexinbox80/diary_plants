<?php

namespace App\Controller\Web\Dashboard\Image\CreateImage;

use App\Controller\Form\ImageType;
use App\Domain\Service\AttachmentService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Image\CreateImage\Input\CreateImageDTO;

final class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(ImageType::class, null, ['is_new' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateImageDTO $createImageDTO */
            $createImageDTO = $form->getData();

            if (!$createImageDTO->groupId) {
                $createImageDTO->groupId = $groupId;
            }

            $this->attachmentService->createFromCreateImageDTO($createImageDTO);

            $message = $this->translator->trans('image.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form->createView(),
            'is_new' => $isNew,
        ];
    }
}

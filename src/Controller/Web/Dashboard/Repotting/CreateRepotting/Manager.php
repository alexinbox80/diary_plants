<?php

namespace App\Controller\Web\Dashboard\Repotting\CreateRepotting;

use App\Controller\Form\RepottingType;
use App\Domain\Service\RepottingService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Repotting\CreateRepotting\Input\CreateRepottingDTO;

final class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(RepottingType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateRepottingDTO $createRepottingDTO */
            $createRepottingDTO = $form->getData();

            if (!$createRepottingDTO->groupId) {
                $createRepottingDTO->groupId = $groupId;
            }

            $this->repottingService->createFromCreateRepottingDTO($createRepottingDTO);

            $message = $this->translator->trans('plant.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

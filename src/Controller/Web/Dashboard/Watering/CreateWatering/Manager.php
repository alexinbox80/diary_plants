<?php

namespace App\Controller\Web\Dashboard\Watering\CreateWatering;

use App\Controller\Form\WateringType;
use App\Domain\Service\WateringService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Watering\CreateWatering\Input\CreateWateringDTO;

final class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(WateringType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateWateringDTO $createWateringDTO */
            $createWateringDTO = $form->getData();

            if (!$createWateringDTO->groupId) {
                $createWateringDTO->groupId = $groupId;
            }

            $this->wateringService->createFromCreateWateringDTO($createWateringDTO);

            $message = $this->translator->trans('watering.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

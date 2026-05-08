<?php

namespace App\Controller\Web\Dashboard\Plant\CreatePlant;

use App\Controller\Form\PlantType;
use App\Domain\Service\PlantService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;

final class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(PlantType::class, null, ['is_new' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreatePlantDTO $createPlantDTO */
            $createPlantDTO = $form->getData();

            if (!$createPlantDTO->groupId) {
                $createPlantDTO->groupId = $groupId;
            }

            $plantModel = $this->plantService->createFromCreatePlantDTO($createPlantDTO);

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

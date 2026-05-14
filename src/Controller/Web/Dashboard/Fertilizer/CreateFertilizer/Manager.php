<?php

namespace App\Controller\Web\Dashboard\Fertilizer\CreateFertilizer;

use App\Controller\Form\FertilizerType;
use App\Domain\Service\FertilizerService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Fertilizer\CreateFertilizer\Input\CreateFertilizerDTO;

final class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(FertilizerType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateFertilizerDTO $createFertilizerDTO */
            $createFertilizerDTO = $form->getData();

            if (!$createFertilizerDTO->groupId) {
                $createFertilizerDTO->groupId = $groupId;
            }

            $this->fertilizerService->createFromCreateFertilizerDTO($createFertilizerDTO);

            $message = $this->translator->trans('fertilizer.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Stimulant\CreateStimulant;

use App\Controller\Form\StimulantType;
use App\Domain\Service\StimulantService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Stimulant\CreateStimulant\Input\CreateStimulantDTO;

final class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(StimulantType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateStimulantDTO $createStimulantDTO */
            $createStimulantDTO = $form->getData();

            if (!$createStimulantDTO->groupId) {
                $createStimulantDTO->groupId = $groupId;
            }

            $this->stimulantService->createFromCreateStimulantDTO($createStimulantDTO);

            $message = $this->translator->trans('stimulant.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

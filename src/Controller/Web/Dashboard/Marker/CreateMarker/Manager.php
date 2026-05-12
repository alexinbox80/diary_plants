<?php

namespace App\Controller\Web\Dashboard\Marker\CreateMarker;

use App\Controller\Form\MarkerType;
use App\Domain\Service\MarkerService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Marker\CreateMarker\Input\CreateMarkerDTO;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(MarkerType::class, null, ['is_new' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateMarkerDTO $createMarkertDTO */
            $createMarkerDTO = $form->getData();

            if (!$createMarkerDTO->groupId) {
                $createMarkerDTO->groupId = $groupId;
            }

            $this->markerService->createFromCreateMarkerDTO($createMarkerDTO);

            $message = $this->translator->trans('marker.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

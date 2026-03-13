<?php

namespace App\Controller\Web\Dashboard\Marker\CreateMarker;

use App\Controller\Form\MarkerType;
use App\Domain\Service\MarkerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Marker\CreateMarker\Input\CreateMarkerDTO;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(MarkerType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateMarkerDTO $createMarkertDTO */
            $createMarkerDTO = $form->getData();

            $this->markerService->createFromCreateMarkerDTO($createMarkerDTO);

            $request->getSession()->getFlashBag()->add('success', 'Сокращение создано.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}

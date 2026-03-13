<?php

namespace App\Controller\Web\Dashboard\Marker\EditMarker;

use App\Domain\Entity\Marker;
use App\Domain\Service\MarkerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Marker\EditMarker\Input\EditMarkerDTO;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Marker $marker): array
    {
        $formData = new EditMarkerDTO(
            $marker->getGroup()->getId(),
            $marker->getLetter(),
            $marker->getColor(),
            $marker->getType()->value,
            $marker->getDescription(),
            $marker->getColorDescription(),
        );

        $form = $this->formFactory->create(Marker::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditMarkerDTO $editMarkerDTO */
            $editMarkerDTO = $form->getData();

            $this->markerService->updateFromEditMarkerDTO($marker, $editMarkerDTO);

            $request->getSession()->getFlashBag()->add('success', 'Сокращение успешно обновлено.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'marker' => $marker
        ];
    }
}

<?php

namespace App\Controller\Web\Dashboard\Plant\EditPlant;

use App\Domain\Entity\Plant;
use App\Controller\Form\PlantType;
use App\Domain\Service\PlantService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Plant\EditPlant\Input\EditPlantDTO;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Plant $plant): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $plant)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $formData = new EditPlantDTO(
            $plant->getGroup()->getId(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $plant->getDescription(),
            $plant->getPurchaseInfo()->getPurchaseDate(),
            $plant->getLifeCycle()->getVaccinationDate(),
            $plant->getLifeCycle()->getPlantingDate(),
            $plant->getPurchaseInfo()->getSeller(),
            $plant->getPurchaseInfo()->getNursery(),
            $plant->getPurchaseInfo()->getPrice(),
            $plant->getPurchaseInfo()->getShippingCost(),
            $plant->getPurchaseInfo()->getPackagingCost(),
            $plant->getLifeCycle()->getSoil(),
            $plant->getSalesInfo()->isSold(),
            $plant->getSalesInfo()->getSellingDate(),
            $plant->getSalesInfo()->getSellingPrice(),
            $plant->getComment()
        );

        $groupId = $plant->getGroup()->getId();

        $form = $this->formFactory->create(PlantType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditPlantDTO $editPlantDTO */
            $editPlantDTO = $form->getData();

            if (!$editPlantDTO->groupId) {
                $editPlantDTO->groupId = $groupId;
            }

            $data = $request->request->all()['plant'] ?? [];
            $editPlantDTO->isShown = (bool) ($data['isShown'] ?? false);
            $editPlantDTO->isSold = (bool) ($data['isSold'] ?? false);

            $this->plantService->updateFromEditPlantDTO($plant, $editPlantDTO);

            $message = $this->translator->trans('plant.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'plant' => $plant
        ];
    }
}

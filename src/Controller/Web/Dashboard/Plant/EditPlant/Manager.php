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

        $groupId = $plant->getGroup()->getId();

        $formData = new EditPlantDTO(
            groupId: $groupId,
            title: $plant->getTitle(),
            room: $plant->getRoom(),
            isShown: $plant->isShown(),
            description: $plant->getDescription(),
            purchaseDate: $plant->getPurchaseInfo()->getPurchaseDate(),
            vaccinationDate: $plant->getLifeCycle()->getVaccinationDate(),
            plantingDate: $plant->getLifeCycle()->getPlantingDate(),
            seller: $plant->getPurchaseInfo()->getSeller(),
            nursery: $plant->getPurchaseInfo()->getNursery(),
            price: $plant->getPurchaseInfo()->getPrice(),
            shippingCost: $plant->getPurchaseInfo()->getShippingCost(),
            packagingCost: $plant->getPurchaseInfo()->getPackagingCost(),
            soil: $plant->getLifeCycle()->getSoil(),
            isSold: $plant->getSalesInfo()->isSold(),
            sellingDate: $plant->getSalesInfo()->getSellingDate(),
            sellingPrice: $plant->getSalesInfo()->getSellingPrice(),
            comment: $plant->getComment()
        );

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
